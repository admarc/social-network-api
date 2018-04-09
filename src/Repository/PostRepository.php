<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\ORM\EntityRepository;

class PostRepository extends EntityRepository
{
    public function findPosts(): array
    {
        $repository = $this->getEntityManager()
            ->getRepository(Post::class);

        $queryBuilder = $repository->createQueryBuilder('p')
            ->select('p');

        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }
}
