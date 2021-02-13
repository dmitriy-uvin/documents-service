<?php

namespace App\Http\Controllers;

use App\Constants\DocumentTypes;
use App\Constants\TaskConstants;
use App\Events\TestEvent;
use App\Exceptions\Document\NotRecognizableDocumentTypeException;
use App\Exceptions\Task\TaskNotFoundException;
use App\Models\Task;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentsController extends Controller
{
    private string $dbrainApiUrl;
    private string $dbrainToken;

    public function __construct()
    {
        $this->dbrainApiUrl = config('dbrain.api_url');
        $this->dbrainToken = config('dbrain.token');
    }

    public function index()
    {
        return view('documents');
    }

    public function getClassifyTasks(Request $request)
    {
        $documents = $request->file('documents');

        $tasks = [];
        foreach ($documents as $document) {
            $response = Http::attach(
                'image',
                fopen($document, 'r'),
                $document->getClientOriginalName()
            )->post("{$this->dbrainApiUrl}/classify?token={$this->dbrainToken}&async=true");

            $tasks[] = $response->json('task_id');
        }

        $responses = [];
        foreach ($tasks as $taskId) {
            $classifyResponse = Http::get(
                "{$this->dbrainApiUrl}/result/{$taskId}?token={$this->dbrainToken}"
            )->json();

            while($classifyResponse['code'] == 202) {
                $classifyResponse = Http::get(
                    "{$this->dbrainApiUrl}/result/{$taskId}?token={$this->dbrainToken}"
                )->json();
            }

            foreach ($classifyResponse['items'] as $item) {
                $image_parts = explode(";base64,", $item['crop']);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                $name = time() . '_' . Str::random(10) . '.' . $image_type;
                $this->saveDocument($image_base64, $name);

                $task = new Task([
                    'user_id' => Auth::id(),
                    'document_path' => 'documents/' . $name,
                    'task_id' => $taskId,
                    'type' => TaskConstants::CLASSIFY_TYPE,
                    'document_type' => $item['document']['type']
                ]);
                $task->save();
                $responses[] = $task;
            }
        }

        return response()->json($responses);
    }

    public function getRecognizeTask(string $id)
    {
        $task = Task::find($id);

        if (!$task) {
            throw new TaskNotFoundException();
        }

        if (!in_array($task->document_type, array_keys(DocumentTypes::recognizableDocumentTypes()))) {
            throw new NotRecognizableDocumentTypeException();
        }

        $document = fopen(storage_path('app/public/' . $task->document_path), 'r');
        $recognizeResponse = Http::attach(
            'image',
            $document,
            str_replace('documents/', '', $document)
        )->post("{$this->dbrainApiUrl}/recognize?token={$this->dbrainToken}&async=true")->json();

        $response = Http::get(
            "{$this->dbrainApiUrl}/result/{$recognizeResponse['task_id']}?token={$this->dbrainToken}"
        )->json();
        while($response['code'] == 202) {
            $response = Http::get(
                "{$this->dbrainApiUrl}/result/{$recognizeResponse['task_id']}?token={$this->dbrainToken}"
            )->json();
        }

        return response()->json($response);
    }

    private function saveDocument($document, $name)
    {
        Storage::put('public/documents/' . $name, $document);
    }
}
