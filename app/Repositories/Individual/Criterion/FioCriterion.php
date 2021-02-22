<?php

namespace App\Repositories\Individual\Criterion;

use App\Constants\FieldTypes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class FioCriterion
{
    private string $fio;

    public function __construct(string $name, string $surname, string $patronymic)
    {
        $this->fio = $surname . ' ' . $name . ' ' . $patronymic;
    }

    public function apply(Builder $builder)
    {
        $fio = $this->fio;
        return $builder->whereHas('fields', function ($query) use ($fio) {
            return $query->whereIn('type', FieldTypes::getFioTypes())
                ->where(DB::raw('LOWER(value)'), 'like', '%' . $fio . '%');
        });
    }
}
