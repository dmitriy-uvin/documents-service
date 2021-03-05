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
            $this->checkIfIndividualExists($payloadData[$dbrainTaskKey]);

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

    private function checkIfIndividualExists(array $documentsPayload): void
    {
        foreach ($documentsPayload as $taskId => $item) {
            $findName = '';
            $findSurname = '';
            $findPatronymic = '';
            $findFio = '';
            foreach ($item['fields'] as $type => $field) {
                if (in_array($type, FieldTypes::getNameTypes(), true)) {
                    $findName = Str::lower($field['text']);
                }
                if (in_array($type, FieldTypes::getSurnameTypes(), true)) {
                    $findSurname = Str::lower($field['text']);
                }
                if (in_array($type, FieldTypes::getPatronymicTypes(), true)) {
                    $findPatronymic = Str::lower($field['text']);
                }
                if (in_array($type, FieldTypes::getFioTypes(), true)) {
                    $findFio = Str::lower($field['text']);
                }
            }

            $docs = Document::all();
            foreach ($docs as $findDoc) {
                $perc = 0;
                $percents = 0;
                $counter = 0;

                if (!$findFio) {
                    $docName = $findDoc
                        ->fields()
                        ->whereIn('type', FieldTypes::getNameTypes())
                        ->where('value', 'like', $findName)
                        ->get()->first();
                    $docName = $docName ? Str::lower($docName->value) : null;

                    $docSurname = $findDoc
                        ->fields()
                        ->whereIn('type', FieldTypes::getSurnameTypes())
                        ->where('value', 'like', $findSurname)
                        ->get()->first();
                    $docSurname = $docSurname ? Str::lower($docSurname->value) : null;

                    $docPatronymic = $findDoc
                        ->fields()
                        ->whereIn('type', FieldTypes::getPatronymicTypes())
                        ->where('value', 'like', $findPatronymic)
                        ->get()->first();
                    $docPatronymic = $docPatronymic ? Str::lower($docPatronymic->value) : null;

                    $docFio = $findDoc
                        ->fields()
                        ->whereIn('type', FieldTypes::getFioTypes())
                        ->where('value', 'like', $findSurname . ' ' . $findName . ' ' . $findPatronymic)
                        ->get()->first();
                    $docFio = $docFio ? Str::lower($docFio->value) : null;
                } else {
                    $docName = $findDoc
                        ->fields()
                        ->whereIn('type', FieldTypes::getNameTypes())
                        ->where('value', 'like', explode(' ', $findFio)[1])
                        ->get()->first();
                    $docName = $docName ? Str::lower($docName->value) : null;

                    $docSurname = $findDoc
                        ->fields()
                        ->whereIn('type', FieldTypes::getSurnameTypes())
                        ->where('value', 'like', explode(' ', $findFio)[0])
                        ->get()->first();
                    $docSurname = $docSurname ? Str::lower($docSurname->value) : null;

                    $docPatronymic = $findDoc
                        ->fields()
                        ->whereIn('type', FieldTypes::getPatronymicTypes())
                        ->where('value', 'like', explode(' ', $findFio)[2])
                        ->get()->first();
                    $docPatronymic = $docPatronymic ? Str::lower($docPatronymic->value) : null;

                    $docFio = $findDoc
                        ->fields()
                        ->whereIn('type', FieldTypes::getFioTypes())
                        ->where('value', 'like', $findFio)
                        ->get()->first();
                    $docFio = $docFio ? Str::lower($docFio->value) : null;
                }

                if (!$findFio) {
                    if (!$docFio) {
                        if ($docName && $findName) {
                            ++$counter;
                            similar_text($docName, $findName, $perc);
                            $percents += $perc;
                        }

                        if ($docSurname && $findSurname) {
                            ++$counter;
                            similar_text($docSurname, $findSurname, $perc);
                            $percents += $perc;
                        }

                        if ($docPatronymic && $findPatronymic) {
                            ++$counter;
                            similar_text($docPatronymic, $findPatronymic, $perc);
                            $percents += $perc;
                        }
                    } else {
                        ++$counter;
                        similar_text($findSurname . ' ' . $findName . ' ' . $findPatronymic, $docFio, $perc);
                        $percents += $perc;
                    }
                } else if (!$docFio) {
                    ++$counter;
                    similar_text($findFio, $docSurname . ' ' . $docName . ' ' . $docPatronymic, $perc);
                    $percents += $perc;
                } else {
                    ++$counter;
                    similar_text($docFio, $findFio, $perc);
                    $percents += $perc;
                }

                $result = 0;
                if ($percents && $counter) {
                    if (
                        ($findFio && $docFio)
                        || ($findFio && !$docFio)
                    ) {
                        if ($counter === 1) {
                            $result = $percents / $counter;
                        }
                    }

                    if (
                        $findName
                        && $findSurname
                        && $findPatronymic
                        && $docFio
                        && $counter === 1
                    ) {
                        $result = $percents / $counter;
                    }

                    if (
                        $findPatronymic
                        && $findName
                        && $findSurname
                        && $docName
                        && $docSurname
                        && $docPatronymic
                        && $counter === 3
                    ) {
                        $result = $percents / $counter;
                    }

                    if ($result > 85) {
                        throw new SuchIndividualAlreadyExistsException(
                            $findDoc->individual->id
                        );
                    }
                }
            }
        }
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
