<?php

namespace App\Interactor\User;

use App\Entity\User;
use App\Interactor\Exception\UserFollowException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;

class FollowUser
{
    private $userRepository;
    private $entityManager;

    public function __construct(UserRepository $userRepository, EntityManager $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    public function execute(User $followee, User $follower)
    {
        if ($followee === $follower) {
            throw new UserFollowException('User can\'t follow himself');
        }

        if ($this->userRepository->isUserFollowedBy($followee, $follower)) {
            throw new UserFollowException(
                sprintf('User %s is already followed by %s', $followee->getUsername(), $follower->getUsername())
            );
        }

        $followee->addFollower($follower);

        $this->entityManager->flush();
    }
}
