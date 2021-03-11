<?php

namespace App\Repositories\Document;

use App\Contracts\EloquentCriterion;
use App\Models\Document;
use App\Models\Individual;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface DocumentRepositoryInterface
{
    public function findByCriteria(EloquentCriterion ...$criteria): Collection;
    public function getById(int $id): ?Document;
    public function delete(Document $document): void;
    public function associateWithIndividual(Document $document, Individual $individual): void;
    public function save(Document $document): Document;
    public function paginatedByCriteria(
        array $criteria
    ): LengthAwarePaginator;
}
