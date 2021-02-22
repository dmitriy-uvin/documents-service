<?php

namespace App\Repositories\Individual\Criterion;

use App\Constants\FieldTypes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PatronymicCriterion
{
    private string $patronymic;

    public function __construct(string $patronymic)
    {
        $this->patronymic = $patronymic;
    }

    public function apply(Builder $builder)
    {
        $patronymic = $this->patronymic;
        return $builder->whereHas('fields', function ($query) use ($patronymic) {
            return $query->whereIn('type', FieldTypes::getPatronymicTypes())
                ->where(DB::raw('LOWER(value)'), 'like', '%' . $patronymic . '%');
        });
    }
}
