<?php

namespace App\Presenters;

use App\Models\Document;
use App\Models\DocumentImage;
use App\Models\Field;
use App\Models\Individual;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ApiPresenter
{
    public function presentCollection(Collection $individuals): Collection
    {
        return $individuals->map(function ($individual) {
            return $this->present($individual);
        });
    }

    public function present(Individual $individual): array
    {
        return [
            'id' => $individual->id,
            'created_at' => $individual->created_at,
            'documents' => $this->presentIndividualDocuments($individual->documents)
        ];
    }

    private function presentIndividualDocuments(Collection $documents): Collection
    {
        return $documents->map(function ($document) {
            return $this->presentDocument($document);
        });
    }

    private function presentDocument(Document $document): array
    {
        return [
            'id' => $document->id,
            'type' => $document->type,
            'created_at' => $document->created_at,
            'last_image' => $document->lastDocumentImage()->path,
            'all_images' => $this->presentDocumentImages($document->documentImage),
            'fields' => $this->presentFields($document->fields)
        ];
    }

    private function presentField(Field $field): array
    {
        return [
            'id' => $field->id,
            'type' => $field->type,
            'value' => $field->value,
            'confidence' => $field->confidence,
            'created_at' => $field->created_at,
            'document_id' => $field->document->id,
        ];
    }

    private function presentFields(Collection $fields): Collection
    {
        return $fields->map(fn ($field) => $this->presentField($field));
    }

    private function presentDocumentImages(Collection $documentImages): Collection
    {
        return $documentImages->map(fn ($docImage) => $this->presentDocumentImage($docImage));
    }

    private function presentDocumentImage(DocumentImage $documentImage): array
    {
        return [
            'path' => url(Storage::url($documentImage->path)),
            'created_at' => $documentImage->created_at
        ];
    }
}
