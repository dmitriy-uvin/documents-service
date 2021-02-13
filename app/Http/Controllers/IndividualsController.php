<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentImage;
use App\Models\DocumentType;
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

    public function save(Request $request)
    {
        /*
         * 1. Создать Individual $individual
         * 2. Проверить на существование DocumentType - $documentType
         * - Создать если не существует
         * 3. Проверить на существования полей FieldTypes к данному DocumentType
         * - Создать если нету
         * 4. Создать Document - $document
         * 5. Создать DocumentImage - $documentImage
         * 6. Создать Fields - $fields
         * 7. Document
         *
         * $taskKey
         * $documentType
         * $fields -> $fieldType -> $value
         */
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
}
