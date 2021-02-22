<?php

namespace App\Repositories\Individual\Criterion;

use Illuminate\Database\Eloquent\Builder;

class PassportNumberCriterion
{
    private string $passportNumber;

    public function __construct(string $passportNumber)
    {
        $this->passportNumber = $passportNumber;
    }

    public function apply(Builder $builder)
    {
        $passportNumber = $this->passportNumber;
        return $builder->whereIn('type', ['passport_main', 'passport_main_handwritten'])
            ->whereHas('fields', function ($query) use ($passportNumber){
                return $query
                    ->where('type', '=', 'series_and_number')
                    ->where('value', 'like', '%' . $passportNumber . '%');
            });
    }
}
