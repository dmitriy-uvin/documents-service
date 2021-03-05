<?php

namespace App\Repositories\Task;

use App\Contracts\EloquentCriterion;
use Illuminate\Support\Collection;

interface TaskRepositoryInterface
{
    public function findByCriteria(EloquentCriterion ...$criteria): Collection;
}
