<?php

namespace App\Repositories\Individual\Criterion;

use App\Constants\FieldTypes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class SurnameCriterion
{
    private string $surname;

    public function __construct(string $surname)
    {
        $this->surname = $surname;
    }

    public function apply(Builder $builder)
    {
        $surname = $this->surname;
        return $builder->whereHas('fields', function ($query) use ($surname) {
            return $query->whereIn('type', FieldTypes::getSurnameTypes())
                ->where(DB::raw('LOWER(value)'), 'like', '%' . $surname . '%');
        });
    }
}
