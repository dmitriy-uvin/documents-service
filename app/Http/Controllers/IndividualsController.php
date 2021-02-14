<?php

namespace App\Http\Controllers;

use App\Exceptions\Individual\IndividualNotFoundException;
use App\Models\Document;
use App\Models\DocumentImage;
use App\Models\Field;
use App\Models\Individual;
use App\Models\Task;
use Illuminate\Http\Request;

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

    public function uploadDocuments(Request $request)
    {
        $individual = Individual::find($request->individual_id);
        if (!$individual) {
            throw new IndividualNotFoundException();
        }

        /*
         * 1. Загружаем документы
         * 2. Отправляем на классификацию
         * 3. Отдаем классифицированные документы
         * 4. Если документы уже существует у юзера, то либо оставляем старую, либо меняем старую на новую
         * 5. Отправляем результат на распознавание и сохранаяем документ
         */
    }
}
