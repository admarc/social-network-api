<?php

namespace App\Interactor\User;

use App\Repository\UserRepository;

class GetUsers
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(?string $name, ?string $surname): array
    {
        return $this->userRepository->findUsers($name, $surname);
    }
}
