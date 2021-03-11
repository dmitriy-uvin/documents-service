<?php

namespace App\Actions\Individual;

final class GetIndividualCollectionRequest
{
    private int $page;
    private int $perPage;
    private ?string $sorting;
    private ?string $direction;

    public function __construct(
        int $page,
        int $perPage,
        ?string $sorting,
        ?string $direction
    ) {
        $this->page = $page;
        $this->perPage = $perPage;
        $this->sorting = $sorting;
        $this->direction = $direction;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getSorting(): ?string
    {
        return $this->sorting;
    }

    public function getDirection(): ?string
    {
        return $this->direction;
    }
}
