<?php

namespace App\Repositories\Individual;

use App\Models\Document;

class IndividualRepository
{
    public function findByDocumentCriteria($criteria)
    {
        $query = Document::query();

        foreach ($criteria as $criterion) {
            $query = $criterion->apply($query);
        }

        return $query->get()->map(fn($document) => $document->individual)->all();
    }
}
