<?php

namespace App\Http\Controllers;

use App\Actions\Individual\GetIndividualByIdAction;
use App\Actions\Individual\GetIndividualByIdRequest;
use App\Actions\Individual\GetIndividualCollectionAction;
use App\Actions\Individual\SearchIndividualAction;
use App\Actions\Individual\SearchIndividualRequest;
use App\Constants\FieldTypes;
use App\Constants\HistoryTypes;
use App\Exceptions\Individual\CantCreateWithoutFioException;
use App\Exceptions\Individual\IndividualNotFoundException;
use App\Exceptions\Individual\SuchIndividualAlreadyExistsException;
use App\Models\Document;
use App\Models\DocumentImage;
use App\Models\Field;
use App\Models\History;
use App\Models\Individual;
use App\Models\Task;
use App\Presenters\IndividualPresenter;
use App\Services\IndividualService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use \Illuminate\Http\JsonResponse;

class IndividualsController extends Controller
{
    private IndividualPresenter $individualPresenter;
    private GetIndividualCollectionAction $getIndividualsAction;
    private GetIndividualByIdAction $getIndividualByIdAction;
    private SearchIndividualAction $searchIndividualAction;

    public function __construct(
        IndividualPresenter $individualPresenter,
        GetIndividualCollectionAction $getIndividualsAction,
        GetIndividualByIdAction $getIndividualByIdAction,
        SearchIndividualAction $searchIndividualAction
    ) {
        $this->individualPresenter = $individualPresenter;
        $this->getIndividualsAction = $getIndividualsAction;
        $this->getIndividualByIdAction = $getIndividualByIdAction;
        $this->searchIndividualAction = $searchIndividualAction;
    }

    public function individualsView()
    {
        return view('individuals');
    }

    public function getIndividuals(): JsonResponse
    {
        $individuals = $this->getIndividualsAction->execute()->getIndividuals();

        return response()->json(
            $this->individualPresenter->presentCollection($individuals)
        );
    }

    public function watchById(string $id)
    {
        return view('individual', [
            'id' => $id
        ]);
    }

    public function getIndividualById(string $id): JsonResponse
    {
        $individual = $this->getIndividualByIdAction->execute(
            new GetIndividualByIdRequest((int)$id)
        )->getIndividual();

        return response()->json(
            $this->individualPresenter->present($individual)
        );
    }

    public function save(Request $request): JsonResponse
    {
        $payloadData = $request->payloadData;
        $response = [];

        $canCreate = false;

        foreach ($payloadData as $dbrainTaskKey => $value) {
            foreach ($payloadData[$dbrainTaskKey] as $taskId => $item) {
                foreach ($item['fields'] as $fieldType => $field) {
                    if (
                        in_array($fieldType, FieldTypes::getNameTypes(), true)
                        ||
                        in_array($fieldType, FieldTypes::getSurnameTypes(), true)
                        ||
                        in_array($fieldType, FieldTypes::getPatronymicTypes(), true)
                        ||
                        in_array($fieldType, FieldTypes::getFioTypes(), true)
                    ) {
                        if ($field['text']) {
                            $canCreate = true;
                        }
                    }
                }
            }
        }

        if (!$canCreate) {
            throw new CantCreateWithoutFioException();
        }

        foreach ($payloadData as $dbrainTaskKey => $value) {
            IndividualService::checkIfIndividualExists($payloadData[$dbrainTaskKey]);

            $individual = new Individual();
            $individual->save();

            foreach ($payloadData[$dbrainTaskKey] as $taskId => $item) {
                $task = Task::find($taskId);

                $document = new Document();
                $document->type = $item['document_type'];
                $document->individual()->associate($individual);
                $document->save();

                History::create([
                    'type' => HistoryTypes::DOCUMENT_ADD,
                    'author_id' => Auth::id(),
                    'document_id' => $document->id,
                    'individual_id' => $individual->id,
                    'before' => $task->document_path
                ]);

                $documentImage = new DocumentImage();
                $documentImage->path = $task->document_path;
                $documentImage->document()->associate($document);
                $documentImage->save();

                foreach ($item['fields'] as $fieldType => $field) {
                    $fieldObj = new Field();
                    $fieldObj->type = $fieldType;
                    $fieldObj->value = $field['text'] ?: '';
                    $fieldObj->confidence = $field['confidence'];
                    $fieldObj->document()->associate($document);
                    $fieldObj->save();
                }
            }
            $response[$dbrainTaskKey] = $individual->id;
        }

        return response()->json($response);
    }

    public function search(Request $request): JsonResponse
    {
        $response = $this->searchIndividualAction->execute(
            new SearchIndividualRequest(
                Str::lower($request->name),
                Str::lower($request->surname),
                Str::lower($request->patronymic),
                $request->snils,
                $request->inn,
                $request->passport,
            )
        );

        return response()->json($response->getIndividuals());
    }
}
