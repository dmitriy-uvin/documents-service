<?php

namespace App\Repositories\Document;

use App\Contracts\EloquentCriterion;
use App\Models\Document;
use App\Models\Individual;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class DocumentRepository implements DocumentRepositoryInterface
{
    private const DEFAULT_PAGE = 1;
    private const DEFAULT_PER_PAGE = 15;
    private const DEFAULT_SORTING = 'desc';
    private const DEFAULT_DIRECTION = 'id';

    public function findByCriteria(EloquentCriterion ...$criteria): Collection
    {
        $query = Document::query();

        foreach ($criteria as $criterion) {
            $query = $criterion->apply($query);
        }

        return $query->get();
    }

    public function paginatedByCriteria(
        array $criteria,
        int $page = self::DEFAULT_PAGE,
        int $perPage = self::DEFAULT_PER_PAGE,
        string $sorting = self::DEFAULT_SORTING,
        string $direction = self::DEFAULT_DIRECTION
    ): LengthAwarePaginator {
        $query = Document::query();

        foreach ($criteria as $criterion) {
            $query = $criterion->apply($query);
        }

        return $query
            ->orderBy($sorting, $direction)
            ->paginate($perPage, ['*'], null, $page);
    }

    public function getById(int $id): ?Document
    {
        return Document::find($id);
    }

    public function delete(Document $document): void
    {
        $document->delete();
    }

    public function associateWithIndividual(Document $document, Individual $individual): void
    {
        $document->individual()->associate($individual);
    }

    public function save(Document $document): Document
    {
        $document->save();
        return $document;
    }
}
