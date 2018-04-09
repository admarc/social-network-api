<?php

namespace App\Interactor\Post;

use App\Entity\Post;
use App\Entity\User;
use App\Interactor\Exception\ValidationException;
use App\Validator\EntityValidator;
use Doctrine\ORM\EntityManagerInterface;

class CreatePost
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
    public function execute(Post $post, User $user)
    {
        $errors = $this->validator->validate($post);

        if (0 !== count($errors)) {
            throw new ValidationException($errors);
        }

        $post->setUser($user);

        $this->entityManager->persist($post);
        $this->entityManager->flush();
    }
}
