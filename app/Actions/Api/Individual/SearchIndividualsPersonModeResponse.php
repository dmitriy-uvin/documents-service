<?php

namespace App\Actions\Api\Individual;

use Illuminate\Support\Collection;

final class SearchIndividualsPersonModeResponse
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
