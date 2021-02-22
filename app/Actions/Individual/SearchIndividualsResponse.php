<?php

namespace App\Actions\Individual;

class SearchIndividualsResponse
{
    private array $individuals;

    public function __construct(array $individuals)
    {
        $this->individuals = $individuals;
    }

    public function getIndividuals(): array
    {
        return $this->individuals;
    }
}
