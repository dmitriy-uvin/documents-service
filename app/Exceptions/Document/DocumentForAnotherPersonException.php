<?php

namespace App\Exceptions\Document;

class DocumentForAnotherPersonException extends \DomainException
{
    protected $message = "Вы не можете загрузить документ другого лица!";
}
