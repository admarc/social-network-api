<?php

namespace App\Interactor\Post;

use App\Entity\Post;
use App\Interactor\Exception\ValidationException;
use App\Validator\EntityValidator;
use Doctrine\ORM\EntityManagerInterface;

class UpdatePost
{
    private $entityManager;
    private $validator;

    public function __construct(EntityManagerInterface $entityManager, EntityValidator $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    /**
     * @throws ValidationException
     */
    public function execute(Post $post)
    {
        $errors = $this->validator->validate($post);

        if (0 !== count($errors)) {
            throw new ValidationException($errors);
        }

        $this->entityManager->flush();
    }
}
