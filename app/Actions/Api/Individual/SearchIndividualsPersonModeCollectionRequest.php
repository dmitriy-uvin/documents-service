<?php

namespace App\Actions\Api\Individual;

final class SearchIndividualsPersonModeCollectionRequest
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
