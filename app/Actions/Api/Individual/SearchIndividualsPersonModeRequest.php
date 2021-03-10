<?php

namespace App\Actions\Api\Individual;

final class SearchIndividualsPersonModeRequest
{
    private ?string $name;
    private ?string $surname;
    private ?string $patronymic;
    private ?string $birthDate;

    public function __construct(
        ?string $name,
        ?string $surname,
        ?string $patronymic,
        ?string $birthDate
    ) {
        $this->name = $name;
        $this->surname = $surname;
        $this->patronymic = $patronymic;
        $this->birthDate = $birthDate;
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

    public function getBirthDate(): ?string
    {
        return $this->birthDate;
    }
}
