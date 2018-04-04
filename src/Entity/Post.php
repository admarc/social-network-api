<?php

namespace App\Entity;

class Post
{
    private $id;
    private $title;
    private $content;
    private $createdAt;
    private $updatedAt;
    private $user;

    public function __construct(string $title, string $content)
    {
        $this->title = $title;
        $this->content = $content;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): user
    {
        return $this->user;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }
}
