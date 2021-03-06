<?php

namespace App\Actions\Individual;

use Illuminate\Support\Collection;

final class GetIndividualCollectionResponse
{
    private Collection $individuals;

    public function __construct(Collection $individuals)
    {
        $this->individuals = $individuals;
    }

    public function getIndividuals(): Collection
    {
        return $this->individuals;
    }
}
