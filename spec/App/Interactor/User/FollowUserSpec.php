<?php

namespace spec\App\Interactor\User;

use App\Entity\User;
use App\Interactor\Exception\UserFollowException;
use App\Interactor\User\FollowUser;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use PhpSpec\ObjectBehavior;

class FollowUserSpec extends ObjectBehavior
{
    public function let(UserRepository $userRepository, EntityManager $entityManager)
    {
        $this->beConstructedWith($userRepository, $entityManager);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(FollowUser::class);
    }

    public function it_should_fail_when_user_try_to_follow_himself()
    {
        $user = new User('Arlen', 'Bales', 'arlen@thesa.com');

        $exception = new UserFollowException('User can\'t follow himself');

        $this->shouldThrow($exception)->duringExecute($user, $user);
    }

    public function it_should_fail_when_user_is_already_followed(UserRepository $userRepository)
    {
        $followee = new User('Arlen', 'Bales', 'arlen@thesa.com');
        $follower = new User('Renna', 'Bales', 'renna@thesa.com');

        $userRepository->isUserFollowedBy($followee, $follower)->shouldBeCalled()->willReturn(true);

        $exception = new UserFollowException('User arlen@thesa.com is already followed by renna@thesa.com');

        $this->shouldThrow($exception)->duringExecute($followee, $follower);
    }

    public function it_should_follow_the_user(UserRepository $userRepository, EntityManager $entityManager)
    {
        $followee = new User('Arlen', 'Bales', 'arlen@thesa.com');
        $follower = new User('Renna', 'Bales', 'renna@thesa.com');

        $userRepository->isUserFollowedBy($followee, $follower)->shouldBeCalled()->willReturn(false);

        $this->execute($followee, $follower);

        $entityManager->flush()->shouldBeCalled();
    }
}
