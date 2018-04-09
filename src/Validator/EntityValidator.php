<?php

namespace App\Validator;

use Symfony\Component\Validator\Validator\ValidatorInterface;

class EntityValidator
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate($entity): array
    {
        $errors = $this->validator->validate($entity);

        if (0 === count($errors)) {
            return [];
        }

        $errorsArray = [];
        foreach ($errors as $error) {
            $errorsArray[$error->getPropertyPath()][] = $error->getMessage();
        }

        return $errorsArray;
    }
}
