<?php

namespace App\Repositories\Document\Criterion;

use App\Constants\DocumentTypes;
use App\Constants\FieldTypes;
use App\Contracts\EloquentCriterion;
use Illuminate\Database\Eloquent\Builder;

class PassportNumberCriterion implements EloquentCriterion
{
    private string $passportNumber;

    public function __construct(string $passportNumber)
    {
        $this->passportNumber = $passportNumber;
    }

    public function apply(Builder $builder): Builder
    {
        $passportNumber = $this->passportNumber;
        return $builder->whereIn('type', DocumentTypes::passportTypes())
            ->whereHas('fields', function ($query) use ($passportNumber){
                return $query
                    ->whereIn('type', FieldTypes::passportNumberTypes())
                    ->where('value', 'like', '%' . $passportNumber . '%');
            });
    }
}
