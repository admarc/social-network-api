<?php

namespace spec\App\Interactor\User;

use App\Interactor\User\GetUsers;
use App\Repository\UserRepository;
use PhpSpec\ObjectBehavior;

class GetUsersSpec extends ObjectBehavior
{
    public function let(UserRepository $userRepository)
    {
        $this->beConstructedWith($userRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(GetUsers::class);
    }

    public function it_should_return_filtered_users(UserRepository $userRepository)
    {
        $users = ['Arlen'];

        $userRepository->findUsers('Arlen', 'Bales')->shouldBeCalled()->willReturn($users);
        $this->execute('Arlen', 'Bales')->shouldReturn($users);
    }
}
