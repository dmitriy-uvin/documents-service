<?php

namespace App\Exceptions\Individual;

use App\Exceptions\BaseException;
use Throwable;

class SuchIndividualAlreadyExistsException extends BaseException
{
    protected $message = "Физическое лицо с данными докментами уже существует!";

    public function __construct($code = 0, $type = "", $message = "", Throwable $previous = null)
    {
        $message = $this->message;
        parent::__construct($code, $type, $message, $previous);
    }
}
