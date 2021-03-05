<?php

namespace App\Repositories\Document;

use App\Contracts\EloquentCriterion;
use Illuminate\Support\Collection;

interface DocumentRepositoryInterface
{
    public function findByCriteria(EloquentCriterion ...$criteria): Collection;
}
