<?php

namespace spec\App\Interactor\Post;

use App\Interactor\Post\GetPosts;
use App\Repository\PostRepository;
use PhpSpec\ObjectBehavior;

class GetPostsSpec extends ObjectBehavior
{
    public function let(PostRepository $postRepository)
    {
        $this->beConstructedWith($postRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(GetPosts::class);
    }

    public function it_should_return_posts(PostRepository $postRepository)
    {
        $posts = ['Post 1'];
        $postRepository->findPosts()->shouldBeCalled()->willReturn($posts);
        $this->execute()->shouldReturn($posts);
    }
}
