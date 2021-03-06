<?php

namespace App\Actions\Individual;

class SaveIndividualResponse
{
    private array $response;

    public function __construct(array $response)
    {
        $this->response = $response;
    }

    public function getResponse(): array
    {
        return $this->response;
    }
}
