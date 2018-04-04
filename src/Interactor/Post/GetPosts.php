<?php

namespace App\Interactor\Post;

use App\Repository\PostRepository;

class GetPosts
{
    private $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function execute(): array
    {
        return $this->postRepository->findPosts();
    }
}
