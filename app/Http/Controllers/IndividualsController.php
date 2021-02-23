<?php

namespace App\Http\Controllers;

use App\Actions\Individual\SearchIndividualsAction;
use App\Actions\Individual\SearchIndividualsRequest;
use App\Constants\FieldTypes;
use App\Exceptions\Individual\CantCreateWithoutFioException;
use App\Exceptions\Individual\IndividualNotFoundException;
use App\Exceptions\Individual\SuchIndividualAlreadyExistsException;
use App\Exceptions\SomethingWentWrongException;
use App\Models\Document;
use App\Models\DocumentImage;
use App\Models\Field;
use App\Models\History;
use App\Models\Individual;
use App\Models\Task;
use App\Presenters\IndividualPresenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class IndividualsController extends Controller
{
    private string $dbrainApiUrl;
    private string $dbrainToken;
    private IndividualPresenter $individualPresenter;
    private SearchIndividualsAction $searchIndividualsAction;

    public function __construct(
        IndividualPresenter $individualPresenter,
        SearchIndividualsAction $searchIndividualsAction
    ) {
        $this->dbrainApiUrl = config('dbrain.api_url');
        $this->dbrainToken = config('dbrain.token');
        $this->individualPresenter = $individualPresenter;
        $this->searchIndividualsAction = $searchIndividualsAction;
    }

    public function individualsView()
    {
        return view('individuals');
    }

    public function getIndividuals()
    {
        $individuals = Individual::has('documents', '>=', 1)->orderBy('created_at', 'desc')->get();

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

    public function getIndividualById(string $id)
    {
        $individual = Individual::find($id);

        if (!$individual) {
            throw new IndividualNotFoundException();
        }

        return response()->json(
            $this->individualPresenter->present($individual)
        );
    }

    public function save(Request $request)
    {
        $payloadData = $request->payloadData;
        $response = [];

        $canCreate = false;

        foreach ($payloadData as $dbrainTaskKey => $value) {
            foreach ($payloadData[$dbrainTaskKey] as $taskId => $item) {
                foreach ($item['fields'] as $fieldType => $field) {
                    if (
                        in_array($fieldType, FieldTypes::getNameTypes())
                    ||
                        in_array($fieldType, FieldTypes::getSurnameTypes())
                        ||
                        in_array($fieldType, FieldTypes::getPatronymicTypes())
                        ||
                        in_array($fieldType, FieldTypes::getFioTypes())
                    ) {
                        if ($field['text']) $canCreate = true;
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
                    'type' => 'document_add',
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

    private function checkIfIndividualExists(array $documentsPayload)
    {
        foreach ($documentsPayload as $taskId => $item) {
            $findName = '';
            $findNameType = '';
            $findSurname = '';
            $findSurnameType = '';
            $findPatronymic = '';
            $findPatronymicType = '';
            $findFio = '';
            $findFioType = '';
            foreach ($item['fields'] as $type => $field) {
                if (in_array($type, FieldTypes::getNameTypes())) {
                    $findName = Str::lower($field['text']);
                    $findNameType = $type;
                }
                if (in_array($type, FieldTypes::getSurnameTypes())) {
                    $findSurname = Str::lower($field['text']);
                    $findSurnameType = $type;
                }
                if (in_array($type, FieldTypes::getPatronymicTypes())) {
                    $findPatronymic = Str::lower($field['text']);
                    $findPatronymicType = $type;
                }
                if (in_array($type, FieldTypes::getFioTypes())) {
                    $findFio = Str::lower($field['text']);
                    $findFioType = $type;
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
                        ->whereIn('type' , FieldTypes::getNameTypes())
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
                        ->whereIn('type' , FieldTypes::getNameTypes())
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
                            $counter = $counter + 1;
                            similar_text($docName, $findName, $perc);
                            $percents += $perc;
                        }

                        if ($docSurname && $findSurname) {
                            $counter = $counter + 1;
                            similar_text($docSurname, $findSurname, $perc);
                            $percents += $perc;
                        }

                        if ($docPatronymic && $findPatronymic) {
                            $counter = $counter + 1;
                            similar_text($docPatronymic, $findPatronymic, $perc);
                            $percents += $perc;
                        }
                    } else {
                        $counter = $counter + 1;
                        similar_text($findSurname . ' ' . $findName . ' ' . $findPatronymic, $docFio, $perc);
                        $percents += $perc;
                    }
                } else {
                    if (!$docFio) {
                        $counter = $counter + 1;
                        similar_text($findFio, $docSurname . ' ' . $docName . ' ' . $docPatronymic, $perc);
                        $percents += $perc;
                    } else {
                        $counter = $counter + 1;
                        similar_text($docFio, $findFio, $perc);
                        $percents += $perc;
                    }
                }
//                throw new SomethingWentWrongException(
//                    $docName
//                );
                if ($percents && $counter) {
                    $result = $percents / $counter;
                    if ($result > 85) {
                        throw new SomethingWentWrongException(
                            "Лицо с таким ФИО скорее всего уже существует!", $result
                        );
                    }
                }
            }
        }
//        throw new SomethingWentWrongException(
//            777
//        );
    }

    public function search(Request $request)
    {
        $individuals = $this->searchIndividualsAction->execute(
            new SearchIndividualsRequest(
                $request->snils,
                $request->inn,
                $request->passport,
                $request->name,
                $request->surname,
                $request->patronymic,
            )
        )->getIndividuals();

        return response()->json($individuals);
    }
}
