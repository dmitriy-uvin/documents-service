<?php

namespace App\Exceptions\Individual;

class SuchIndividualAlreadyExistsException extends \DomainException
{
    protected $message = "Физическое лицо с данными докментами уже существует!";
}
