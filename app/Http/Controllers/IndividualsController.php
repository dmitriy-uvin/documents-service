<?php

namespace App\Http\Controllers;

use App\Constants\FieldTypes;
use App\Exceptions\Individual\IndividualNotFoundException;
use App\Models\Document;
use App\Models\DocumentImage;
use App\Models\Field;
use App\Models\FieldHistory;
use App\Models\Individual;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndividualsController extends Controller
{
    public function individualsView()
    {
        return view('individuals');
    }

    public function getIndividuals()
    {
        return response()->json(
            Individual::all()
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

//        $history = FieldHistory::where('individual_id', '=', $individual->id)->get()->all();
        return response()->json($individual);
    }

    public function save(Request $request)
    {
        $payloadData = $request->payloadData;
        foreach ($payloadData as $dbrainTaskKey => $value) {
            $individual = new Individual();
            $individual->save();
            foreach ($payloadData[$dbrainTaskKey] as $taskId => $item) {
                $task = Task::find($taskId);

                $document = new Document();
                $document->type = $item['document_type'];
                $document->individual()->associate($individual);
                $document->save();

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
                $documentQuery->whereHas('fields', function ($query) use ($name) {
                    return $query->whereIn('type', FieldTypes::getSurnameTypes())
                        ->where(DB::raw('LOWER(value)'), 'like', '%' . $name . '%');
                });
            }

            if ($patronymic) {
                $documentQuery->whereHas('fields', function ($query) use ($name) {
                    return $query->whereIn('type', FieldTypes::getPatronymicTypes())
                        ->where(DB::raw('LOWER(value)'), 'like', '%' . $name . '%');
                });
            }
        }

        $individualsByDocuments = $documentQuery->get()->map(fn($document) => $document->individual)->all();

        $individuals = $individualsByDocuments;

        return response()->json($individuals);
    }
}
