<?php

namespace spec\App\Interactor\Post;

use App\Interactor\Post\UpdatePost;
use App\Interactor\Exception\ValidationException;
use App\Entity\Post;
use App\Validator\EntityValidator;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;

class UpdatePostSpec extends ObjectBehavior
{
    public function let(EntityManagerInterface $entityManager, EntityValidator $validator)
    {
        $this->beConstructedWith($entityManager, $validator);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(UpdatePost::class);
    }

    public function it_should_fail_when_post_data_is_invalid(
        EntityValidator $validator
    ) {
        $post = new Post('', 'Some content');

        $errors = ['name' => ['not_blank']];

        $validator->validate($post)->shouldBeCalled()->willReturn($errors);

        $this->shouldThrow(new ValidationException($errors))->duringExecute($post);
    }

    public function it_should_update_post_for_user(
        EntityManagerInterface $entityManager,
        EntityValidator $validator
    ) {
        $post = new Post('', 'Some content');

        $validator->validate($post)->shouldBeCalled()->willReturn([]);
        $entityManager->flush()->shouldBeCalled();

        $this->execute($post);
    }
}
