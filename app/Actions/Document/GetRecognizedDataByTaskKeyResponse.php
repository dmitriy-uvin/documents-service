<?php

namespace App\Actions\Document;

class GetRecognizedDataByTaskKeyResponse
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