<?php

namespace App\Http\Controllers;

use App\Actions\Document\AddDocumentForIndividualAction;
use App\Actions\Document\AddDocumentForIndividualRequest;
use App\Actions\Document\DeleteDocumentByIdAction;
use App\Actions\Document\DeleteDocumentByIdRequest;
use App\Actions\Field\UpdateFieldByIdAction;
use App\Actions\Field\UpdateFieldByIdRequest;
use App\Constants\HistoryTypes;
use App\Constants\TaskTypes;
use App\Exceptions\Document\DocumentForAnotherPersonException;
use App\Exceptions\Document\DocumentNotFoundException;
use App\Exceptions\Document\UnableToDeleteDocumentException;
use App\Exceptions\Field\FieldNotFoundException;
use App\Exceptions\Individual\IndividualNotFoundException;
use App\Exceptions\Task\TaskNotFoundException;
use App\Http\Requests\AddDocumentForIndividual;
use App\Http\Requests\UpdateFieldByIdHttpRequest;
use App\Models\Document;
use App\Models\DocumentImage;
use App\Models\Field;
use App\Models\History;
use App\Models\Individual;
use App\Models\Task;
use App\Presenters\DocumentPresenter;
use App\Presenters\FieldPresenter;
use App\Services\DbrainApiService;
use App\Services\FioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use \Illuminate\Http\JsonResponse;

class DocumentsController extends Controller
{
    private DbrainApiService $apiService;
    private DeleteDocumentByIdAction $deleteDocumentByIdAction;
    private AddDocumentForIndividualAction $addDocumentForIndividualAction;
    private DocumentPresenter $documentPresenter;
    private FieldPresenter $fieldPresenter;
    private UpdateFieldByIdAction $updateFieldByIdAction;

    public function __construct(
        DbrainApiService $service,
        DeleteDocumentByIdAction $deleteDocumentByIdAction,
        AddDocumentForIndividualAction $addDocumentForIndividualAction,
        UpdateFieldByIdAction $updateFieldByIdAction,
        DocumentPresenter $documentPresenter,
        FieldPresenter $fieldPresenter
    ) {
        $this->apiService = $service;
        $this->deleteDocumentByIdAction = $deleteDocumentByIdAction;
        $this->addDocumentForIndividualAction = $addDocumentForIndividualAction;
        $this->updateFieldByIdAction = $updateFieldByIdAction;
        $this->documentPresenter = $documentPresenter;
        $this->fieldPresenter = $fieldPresenter;
    }

    public function index()
    {
        return view('documents');
    }

    public function getClassifyTasks(Request $request): JsonResponse
    {
        $documents = $request->file('documents');

        $tasks = [];
        foreach ($documents as $document) {
            $tasks[] = $this->apiService->getClassifyTaskId($document);
        }

        $responses = [];
        foreach ($tasks as $taskId) {
            $classifyResponse = $this->apiService->getClassifyResponse($taskId);

            foreach ($classifyResponse['items'] as $item) {
                $image_parts = explode(";base64,", $item['crop']);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                $name = time() . '_' . Str::random(10) . '.' . $image_type;
                $this->saveDocument($image_base64, $name);

                $task = Task::create([
                    'user_id' => Auth::id(),
                    'document_path' => 'documents/' . $name,
                    'task_id' => $taskId,
                    'type' => TaskTypes::CLASSIFY_TYPE,
                    'document_type' => $item['document']['type']
                ]);
                $responses[] = $task;
            }
        }

        return response()->json($responses);
    }

    public function getRecognizeTask(string $id): JsonResponse
    {
        $task = Task::find($id);

        if (!$task) {
            throw new TaskNotFoundException();
        }

        $document = fopen(storage_path('app/public/' . $task->document_path), 'rb');
        $recognizeTaskId = $this->apiService->getRecognizeTaskId($document);
        fclose($document);
        $response = $this->apiService->getRecognizeResponse($recognizeTaskId);

        return response()->json($response);
    }

    public function replaceDocument(Request $request): JsonResponse
    {
        $task = Task::find($request->task_id);

        if (!$task) {
            throw new TaskNotFoundException();
        }

        $documentObj = Document::find($request->document_id);
        $pathBefore = $documentObj->lastDocumentImage()->path;

        $document = fopen(storage_path('app/public/' . $task->document_path), 'rb');
        $recognizeTaskId = $this->apiService->getRecognizeTaskId($document);

        $response = $this->apiService->getRecognizeResponse($recognizeTaskId);

        $newDocImage = new DocumentImage();
        $newDocImage->path = $task->document_path;
        $newDocImage->document()->associate($documentObj);
        $newDocImage->save();

        $documentObj->fields()->delete();
        $documentObj->save();

        History::create([
            'type' => 'document_update',
            'author_id' => Auth::id(),
            'document_id' => $documentObj->id,
            'individual_id' => $documentObj->individual->id,
            'before' => $pathBefore,
            'after' => $documentObj->lastDocumentImage()->path
        ]);

        foreach ($response['items'][0]['fields'] as $fieldType => $field) {
            $fieldObj = new Field();
            $fieldObj->type = $fieldType;
            $fieldObj->value = $field['text'] ?: '';
            $fieldObj->confidence = $field['confidence'];
            $fieldObj->document()->associate($documentObj);
            $fieldObj->save();
        }

        return response()->json();
    }

    public function addDocumentForIndividual(AddDocumentForIndividual $request): JsonResponse
    {
        $response = $this->addDocumentForIndividualAction->execute(
            new AddDocumentForIndividualRequest(
                (int)$request->task_id,
                (int)$request->individual_id,
                (bool)$request->force,
            )
        );

        return response()->json(
            $this->documentPresenter->present($response->getDocument())
        );
    }

    public function updateField(UpdateFieldByIdHttpRequest $request): JsonResponse
    {
        $response = $this->updateFieldByIdAction->execute(
            new UpdateFieldByIdRequest(
                (int)$request->field_id,
                $request->new_value
            )
        );

        return response()->json(
            $this->fieldPresenter->present(
                $response->getField()
            )
        );
    }

    public function getRecognizedDataByTaskKey(Request $request): JsonResponse
    {
        $tasks = Task::where('task_id', '=', $request->task_key)->get()->all();

        $allResponses = [];
        foreach ($tasks as $task) {
            if ($task->document_type !== 'not_document') {
                $document = fopen(storage_path('app/public/' . $task->document_path), 'rb');
                $recognizeTaskId = $this->apiService->getRecognizeTaskId($document);
                fclose($document);
                $response = $this->apiService->getRecognizeResponse($recognizeTaskId);
                $allResponses[] = [
                    'id' => $task->id,
                    'task_id' => $task->task_id,
                    'doc_type' => $response['items'][0]['doc_type'],
                    'fields' => $response['items'][0]['fields'],
                ];
            }
        }

        return response()->json($allResponses);
    }

    public function deleteDocument(string $id): JsonResponse
    {
        $this->deleteDocumentByIdAction->execute(
            new DeleteDocumentByIdRequest((int)$id)
        );

        return response()->json(null, 204);
    }

    private function saveDocument($document, $name): void
    {
        Storage::put('public/documents/' . $name, $document);
    }
}
