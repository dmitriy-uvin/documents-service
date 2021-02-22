<?php

namespace App\Actions\Individual;

class SearchIndividualsRequest
{
    private ?string $snilsNumber;
    private ?string $innNumber;
    private ?string $passportNumber;

    private ?string $name;
    private ?string $surname;
    private ?string $patronymic;

    public function __construct(
        ?string $snilsNumber,
        ?string $innNumber,
        ?string $passportNumber,
        ?string $name,
        ?string $surname,
        ?string $patronymic
    ) {
        $this->snilsNumber = $snilsNumber;
        $this->innNumber = $innNumber;
        $this->passportNumber = $passportNumber;
        $this->name = $name;
        $this->surname = $surname;
        $this->patronymic = $patronymic;
    }

    public function getSnilsNumber(): ?string
    {
        return $this->snilsNumber;
    }

    public function getInnNumber(): ?string
    {
        return $this->innNumber;
    }

    public function getPassportNumber(): ?string
    {
        return $this->passportNumber;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function getPatronymic(): ?string
    {
        return $this->patronymic;
    }
}
