<?php

namespace App\Exceptions;

use Throwable;

class BaseException extends \DomainException
{
    private $type;

    public function __construct($code = 0, $type = "", $message = "", Throwable $previous = null)
    {
        $this->type = $type;
        parent::__construct($message, $code, $previous);
    }

    public function getType()
    {
        return $this->type;
    }
}
