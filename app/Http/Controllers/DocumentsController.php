<?php

namespace App\Http\Controllers;

use App\Constants\TaskConstants;
use App\Exceptions\Document\DocumentAlreadyRestoredException;
use App\Exceptions\Document\DocumentForAnotherPersonException;
use App\Exceptions\Document\DocumentNotFoundException;
use App\Exceptions\Document\UnableToDeleteDocumentException;
use App\Exceptions\Field\FieldNotFoundException;
use App\Exceptions\Individual\IndividualNotFoundException;
use App\Exceptions\Task\TaskNotFoundException;
use App\Models\Document;
use App\Models\DocumentImage;
use App\Models\Field;
use App\Models\History;
use App\Models\Individual;
use App\Models\Task;
use App\Services\DbrainApiService;
use App\Services\FioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentsController extends Controller
{
    private DbrainApiService $apiService;

    public function __construct(DbrainApiService $service)
    {
        $this->apiService = $service;
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
                    'type' => TaskConstants::CLASSIFY_TYPE,
                    'document_type' => $item['document']['type']
                ]);
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

        $document = fopen(storage_path('app/public/' . $task->document_path), 'r');
        $recognizeTaskId = $this->apiService->getRecognizeTaskId($document);
        fclose($document);
        $response = $this->apiService->getRecognizeResponse($recognizeTaskId);

        return response()->json($response);
    }

    public function replaceDocument(Request $request)
    {
        $task = Task::find($request->task_id);

        if (!$task) {
            throw new TaskNotFoundException();
        }

        $documentObj = Document::find($request->document_id);
        $pathBefore = $documentObj->lastDocumentImage()->path;

        $document = fopen(storage_path('app/public/' . $task->document_path), 'r');
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

        return response()->json([
            "success" => true
        ]);
    }

    public function addDocumentForIndividual(Request $request)
    {
        $task = Task::find($request->task_id);

        if (!$task) {
            throw new TaskNotFoundException();
        }

        $individual = Individual::find($request->individual_id);

        if (!$individual) {
            throw new IndividualNotFoundException();
        }

        $document = fopen(storage_path('app/public/' . $task->document_path), 'r');
        $recognizeTaskId = $this->apiService->getRecognizeTaskId($document);
        $response = $this->apiService->getRecognizeResponse($recognizeTaskId);

        $individualName = FioService::getIndividualName($individual);
        $nameFromResponse = FioService::getNameFromResponse($response);

        if ($individualName && $nameFromResponse) {
            if ($individualName !== $nameFromResponse) {
                throw new DocumentForAnotherPersonException("Вы загружаете документ другого человека! 1");
            }
        }

        $individualSurname = FioService::getIndividualSurname($individual);
        $surnameFromResponse = FioService::getSurnameFromResponse($response);

        if ($individualSurname && $surnameFromResponse) {
            if ($individualSurname !== $surnameFromResponse) {
                throw new DocumentForAnotherPersonException("Вы загружаете документ другого человека! 2");
            }
        }

        $individualPatronymic = FioService::getIndividualPatronymic($individual);
        $patronymicFromResponse = FioService::getPatronymicFromResponse($response);

        if ($individualPatronymic && $patronymicFromResponse) {
            if ($individualPatronymic !== $patronymicFromResponse) {
                throw new DocumentForAnotherPersonException("Вы загружаете документ другого человека! 3");
            }
        }

        $individualFio = FioService::getIndividualFio($individual);
        $fioFromResponse = FioService::getFioFromResponse($response);

        if ($individualFio && $fioFromResponse) {
            if ($individualFio !== $fioFromResponse) {
                throw new DocumentForAnotherPersonException("Вы загружаете документ другого человека! 4");
            }
        }

        $individualBirthDate = FioService::getIndividualBirthDate($individual);
        $birthDateFromResponse = FioService::getBirthDateFromResponse($response);

        if ($individualBirthDate && $birthDateFromResponse) {
            if ($individualBirthDate !== $birthDateFromResponse) {
                throw new DocumentForAnotherPersonException("Вы загружаете документ другого человека! 5");
            }
        }

        $documentObj = new Document();
        $documentObj->type = $task->document_type;
        $documentObj->individual()->associate($individual);
        $documentObj->save();

        $documentImage = new DocumentImage();
        $documentImage->path = $task->document_path;
        $documentImage->document()->associate($documentObj);
        $documentImage->save();

        foreach ($response['items'][0]['fields'] as $fieldType => $field) {
            $fieldObj = new Field();
            $fieldObj->type = $fieldType;
            $fieldObj->value = $field['text'] ?: '';
            $fieldObj->confidence = $field['confidence'];
            $fieldObj->document()->associate($documentObj);
            $fieldObj->save();
        }

        History::create([
            'type' => 'document_add',
            'author_id' => Auth::id(),
            'document_id' => $documentObj->id,
            'individual_id' => $individual->id,
            'before' => $task->document_path
        ]);

        return response()->json([
            "success" => true
        ]);
    }

    public function updateField(Request $request)
    {
        $field = Field::find($request->field_id);

        if (!$field) {
            throw new FieldNotFoundException();
        }

        if ($field->value !== $request->new_value) {
            $field->value = $request->new_value;

            $fHistory = new History();
            $fHistory->type = 'field';
            $fHistory->before = $field->getDifference()['before'];
            $fHistory->after = $field->getDifference()['after'];
            $fHistory->author()->associate(Auth::id());
            $fHistory->field()->associate($field);
            $fHistory->document()->associate($field->document->id);
            $fHistory->individual()->associate($field->document->individual->id);
            $fHistory->save();

            $field->save();
        }

        return response()->json([
            "success" => true
        ]);
    }

    public function getRecognizedDataByTaskKey(Request $request)
    {
        $tasks = Task::where('task_id', '=', $request->task_key)->get()->all();

        $allResponses = [];
        foreach ($tasks as $task) {
            if ($task->document_type !== 'not_document') {
                $document = fopen(storage_path('app/public/' . $task->document_path), 'r');
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

    public function deleteDocument(string $id)
    {
        $document = Document::find($id);

        if (!$document) {
            throw new DocumentNotFoundException();
        }
        $individual = $document->individual;

        if ($individual->documents()->count() <= 1) {
            throw new UnableToDeleteDocumentException();
        }

        $docId = $document->id;

        $document->delete();

        History::create([
            'type' => 'document_delete',
            'author_id' => Auth::id(),
            'individual_id' => $individual->id,
            'document_id' => $docId
        ]);

        return response()->json(null, 204);
    }

    public function restoreDocument(Request $request)
    {
        $document = Document::withTrashed()->find((int)$request->id);

        if (!$document) {
            throw new DocumentNotFoundException();
        }

        if (!$document->deleted_at) {
            throw new DocumentAlreadyRestoredException();
        }

        $document->restore();

        History::create([
            'type' => 'document_restore',
            'author_id' => Auth::id(),
            'individual_id' => $document->individual->id,
            'document_id' => $document->id
        ]);

        return response()->json([
            'success' => true
        ]);
    }

    private function saveDocument($document, $name)
    {
        Storage::put('public/documents/' . $name, $document);
    }
}
