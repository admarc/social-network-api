<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function findUsers(?string $name, ?string $surname): array
    {
        $repository = $this->getEntityManager()
            ->getRepository(User::class);

        $queryBuilder = $repository->createQueryBuilder('u')
            ->select('u');

        if (null !== $name) {
            $queryBuilder->andWhere('u.name = :name')
                ->setParameter('name', $name);
        }

        if (null !== $surname) {
            $queryBuilder->andWhere('u.surname = :surname')
                ->setParameter('surname', $surname);
        }

        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

    public function isUserFollowedBy(User $followee, User $follower): bool
    {
        $repository = $this->getEntityManager()
            ->getRepository(User::class);

        $queryBuilder = $repository->createQueryBuilder('u')
            ->select('u, uf')
            ->innerJoin('u.followers', 'uf')
            ->where('u = :followee')
            ->andWhere('uf.id = :follower')
            ->setParameters([
                'followee' => $followee,
                'follower' => $follower,
            ]);

        $count = $queryBuilder->getQuery()->getOneOrNullResult();

        return (bool) $count;
    }
}
