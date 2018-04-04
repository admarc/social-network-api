<?php

namespace spec\App\Interactor\Post;

use App\Entity\Post;
use App\Interactor\Post\DeletePost;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DeletePostSpec extends ObjectBehavior
{
    public function let(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->beConstructedWith($entityManager, $validator);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(DeletePost::class);
    }

    public function it_should_delete_user_post(
        EntityManagerInterface $entityManager
    ) {
        $post = new Post('', 'Some content');

        $entityManager->remove($post)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $this->execute($post);
    }
}
