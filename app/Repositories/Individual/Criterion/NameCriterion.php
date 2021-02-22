<?php


namespace App\Repositories\Individual\Criterion;


use App\Constants\FieldTypes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class NameCriterion
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function apply(Builder $builder)
    {
        $name = $this->name;
        return $builder->whereHas('fields', function ($query) use ($name) {
            return $query->whereIn('type', FieldTypes::getNameTypes())
                ->where(DB::raw('LOWER(value)'), 'like', '%' . $name . '%');
        });
    }
}
