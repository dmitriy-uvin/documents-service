<?php

namespace App\Http\Controllers;

use App\Constants\FieldTypes;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class IndividualsController extends Controller
{
    private string $dbrainApiUrl;
    private string $dbrainToken;
    private IndividualPresenter $individualPresenter;

    public function __construct(IndividualPresenter $individualPresenter)
    {
        $this->dbrainApiUrl = config('dbrain.api_url');
        $this->dbrainToken = config('dbrain.token');
        $this->individualPresenter = $individualPresenter;
    }

    public function individualsView()
    {
        return view('individuals');
    }

    public function getIndividuals()
    {
        $individuals = Individual::orderBy('created_at', 'desc')->get();

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
        foreach ($documentsPayload as $taskId => $document) {
            $findDocs = Document::where('type', '=', $document['document_type'])->get()->all();
            foreach ($findDocs as $findDoc) {
                $findFields = $findDoc->fields
                    ->flatMap(fn($field) => [$field->type => $field->value]);

                $fields = [];
                foreach ($document['fields'] as $fieldType => $field) {
                    $fields[$fieldType] = $field['text'] ?: '';
                }
                $fields = collect($fields);

                $fieldsCount = $findFields->count();
                $diff = $findFields->diffAssoc($fields);

                if (count($diff) < $fieldsCount / 4) {
                    throw new SuchIndividualAlreadyExistsException();
                }
            }
        }
    }

    public function search(Request $request)
    {
        $snilsNumber = $request->snils;
        $innNumber = $request->inn;
        $passportNumber = $request->passport;

        $name = $request->name;
        $surname = $request->surname;
        $patronymic = $request->patronymic;

        $documentQuery = Document::query();

        if ($snilsNumber) {
            $documentQuery->where('type', '=', 'snils_front')
                ->whereHas('fields', function ($query) use ($snilsNumber){
                    return $query
                        ->where('type', '=', 'number')
                        ->where('value', 'like', '%' . $snilsNumber . '%');
                });
        }

        if ($passportNumber) {
            $documentQuery->whereIn('type', ['passport_main', 'passport_main_handwritten'])
                ->whereHas('fields', function ($query) use ($passportNumber){
                    return $query
                        ->where('type', '=', 'series_and_number')
                        ->where('value', 'like', '%' . $passportNumber . '%');
                });
        }

        if ($innNumber) {
            $documentQuery->where('type', '=', 'inn_person')
                ->whereHas('fields', function ($query) use ($innNumber){
                    return $query
                        ->where('type', '=', 'number')
                        ->where('value', 'like', '%' . $innNumber . '%');
                });
        }

        $fieldQuery = Field::query();

        if ($name && $surname && $patronymic) {
            $fio = $surname . ' ' . $name . ' ' . $patronymic;
            $fieldQuery
                ->whereIn('type', FieldTypes::getFioTypes())
                ->where(DB::raw('LOWER(value)'), 'like', '%' . $fio . '%');
        } else {
            if ($name) {
                $documentQuery->whereHas('fields', function ($query) use ($name) {
                    return $query->whereIn('type', FieldTypes::getNameTypes())
                        ->where(DB::raw('LOWER(value)'), 'like', '%' . $name . '%');
                });
            }

            if ($surname) {
                $documentQuery->whereHas('fields', function ($query) use ($surname) {
                    return $query->whereIn('type', FieldTypes::getSurnameTypes())
                        ->where(DB::raw('LOWER(value)'), 'like', '%' . $surname . '%');
                });
            }

            if ($patronymic) {
                $documentQuery->whereHas('fields', function ($query) use ($patronymic) {
                    return $query->whereIn('type', FieldTypes::getPatronymicTypes())
                        ->where(DB::raw('LOWER(value)'), 'like', '%' . $patronymic . '%');
                });
            }
        }

        $individuals = $documentQuery->get()->map(fn($document) => $document->individual)->all();

        return response()->json($individuals);
    }
}
