<?php

namespace spec\App\Interactor\Post;

use App\Entity\User;
use App\Interactor\Post\CreatePost;
use App\Interactor\Exception\ValidationException;
use App\Entity\Post;
use App\Validator\EntityValidator;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;

class CreatePostSpec extends ObjectBehavior
{
    public function let(EntityManagerInterface $entityManager, EntityValidator $validator)
    {
        $this->beConstructedWith($entityManager, $validator);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(CreatePost::class);
    }

    public function it_should_fail_when_post_data_is_invalid(
        EntityManagerInterface $entityManager,
        EntityValidator $validator
    ) {
        $post = new Post('', 'Some content');
        $user = new User('Arlen', 'Bales', 'arlen@thesa.com');

        $entityManager->persist()->shouldNotBeCalled();

        $errors = ['name' => ['not_blank']];

        $validator->validate($post)->shouldBeCalled()->willReturn($errors);

        $this->shouldThrow(new ValidationException($errors))->duringExecute($post, $user);
    }

    public function it_should_persist_post_for_user(
        EntityManagerInterface $entityManager,
        EntityValidator $validator
    ) {
        $post = new Post('', 'Some content');
        $user = new User('Arlen', 'Bales', 'arlen@thesa.com');

        $entityManager->persist($post)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $validator->validate($post)->shouldBeCalled()->willReturn([]);

        $this->execute($post, $user);
    }
}
