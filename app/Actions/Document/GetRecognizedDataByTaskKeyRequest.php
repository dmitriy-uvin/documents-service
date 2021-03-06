<?php

namespace App\Actions\Document;

class GetRecognizedDataByTaskKeyRequest
{
    private string $taskKey;

    public function __construct(string $taskKey)
    {
        $this->taskKey = $taskKey;
    }

    public function getTaskKey(): string
    {
        return $this->taskKey;
    }
}
