<?php

namespace App\Repositories\Individual;

use App\Contracts\EloquentCriterion;
use App\Models\Document;
use App\Models\Individual;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class IndividualRepository implements IndividualRepositoryInterface
{
    public const DEFAULT_PAGE = 1;
    public const DEFAULT_PER_PAGE = 15;
    public const DEFAULT_SORTING = 'id';
    public const DEFAULT_DIRECTION = 'desc';

    public function findByCriteria(EloquentCriterion ...$criteria): Collection
    {
        $query = Individual::query();

        foreach ($criteria as $criterion) {
            $query = $criterion->apply($query);
        }

        return $query->get();
    }

    public function findById(int $id): ?Individual
    {
        return Individual::find($id);
    }

    public function save(Individual $individual): Individual
    {
        $individual->save();
        return $individual;
    }

    public function paginatedByCriteria(
        array $criteria = [],
        int $page = self::DEFAULT_PAGE,
        int $perPage = self::DEFAULT_PER_PAGE,
        ?string $sorting = self::DEFAULT_SORTING,
        ?string $direction = self::DEFAULT_DIRECTION
    ): LengthAwarePaginator {
        $query = Individual::query();

        foreach ($criteria as $criterion) {
            $query = $criterion->apply($query);
        }

        return $query
            ->orderBy($sorting, $direction)
            ->paginate($perPage, ['*'], null, $page);
    }
}
