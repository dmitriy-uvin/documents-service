<?php

namespace App\Exceptions\Api;

class NoSearchQueryParamsException extends \DomainException
{
    protected $message = "Не найдено ни одного поискового параметра!";
}
