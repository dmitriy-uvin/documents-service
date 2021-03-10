<?php

namespace App\Actions\Api\Individual;

final class SearchIndividualsDocumentModeRequest
{
    private ?string $innNumber;
    private ?string $snilsNumber;
    private ?string $passport;

    public function __construct(
        ?string $innNumber,
        ?string $snilsNumber,
        ?string $passport
    ) {
        $this->innNumber = $innNumber;
        $this->snilsNumber = $snilsNumber;
        $this->passport = $passport;
    }

    public function getInnNumber(): ?string
    {
        return $this->innNumber;
    }

    public function getSnilsNumber(): ?string
    {
        return $this->snilsNumber;
    }

    public function getPassport(): ?string
    {
        return $this->passport;
    }
}
