<?php

namespace App\Repositories\Individual\Criterion;

use Illuminate\Database\Eloquent\Builder;

class SnilsNumberCriterion
{
    private string $snilsNumber;

    public function __construct(string $snilsNumber)
    {
        $this->snilsNumber = $snilsNumber;
    }

    public function apply(Builder $builder)
    {
        $snilsNumber = $this->snilsNumber;
        return $builder->where('type', '=', 'snils_front')
            ->whereHas('fields', function ($query) use ($snilsNumber){
                return $query
                    ->where('type', '=', 'number')
                    ->where('value', 'like', '%' . $snilsNumber . '%');
            });
    }
}
