<?php

namespace App\Repositories\Document\Criterion;

use App\Constants\FieldTypes;
use Illuminate\Database\Eloquent\Builder;
use App\Contracts\EloquentCriterion;

class DateBirthCriterion implements EloquentCriterion
{
    private string $dateBirth;

    public function __construct(string $dateBirth)
    {
        $this->dateBirth = $dateBirth;
    }

    public function apply(Builder $builder): Builder
    {
        $dateBirth = $this->dateBirth;
        return $builder->whereHas('fields', function ($query) use ($dateBirth) {
            return $query->whereIn('type', FieldTypes::getBornFullDateTypes())
                ->where('value', '=', $dateBirth);
        });
    }
}
