<?php

namespace App\Interactor\Exception;

class ValidationException extends \Exception
{
    private $errors = [];

    public function __construct(array $errors, string $message = 'Validation exception')
    {
        $this->message = $message;
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
