<?php

namespace App\Interactor\Post;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;

class DeletePost
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function execute(Post $post)
    {
        $this->entityManager->remove($post);
        $this->entityManager->flush();
    }
}
