<?php

namespace App\Repositories\Document;

use App\Contracts\EloquentCriterion;
use App\Models\Document;
use Illuminate\Support\Collection;

class DocumentRepository implements DocumentRepositoryInterface
{
    public function findByCriteria(EloquentCriterion ...$criteria): Collection
    {
        $query = Document::query();

        foreach ($criteria as $criterion) {
            $query = $criterion->apply($query);
        }

        return $query->get();
    }
}
