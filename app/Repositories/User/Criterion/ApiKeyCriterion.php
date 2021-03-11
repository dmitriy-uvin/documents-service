<?php

namespace App\Repositories\User\Criterion;

use App\Contracts\EloquentCriterion;
use Illuminate\Database\Eloquent\Builder;

class ApiKeyCriterion implements EloquentCriterion
{
    private string $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function apply(Builder $builder): Builder
    {
        return $builder->where('api_key', '=', $this->apiKey);
    }
}
