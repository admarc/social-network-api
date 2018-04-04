<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, \Serializable
{
    const ROLE_USER = 'ROLE_USER';

    private $id;
    private $password;
    private $name;
    private $surname;
    private $email;
    private $posts;
    private $followers;
    private $followees;

    public function __construct(string $name, string $surname, string $email)
    {
        $this->name = $name;
        $this->surname = $surname;
        $this->email = $email;
        $this->posts = new ArrayCollection();
        $this->followees = new ArrayCollection();
        $this->followers = new ArrayCollection();
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function getSalt()
    {
        // with bcrypt salt is not needed
        return null;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles()
    {
        return [self::ROLE_USER];
    }

    public function eraseCredentials()
    {
    }

    public function addFollowee(User $followee)
    {
        if (!$this->followees->contains($followee)) {
            $this->followees->add($followee);
        }
    }

    public function addFollower(User $follower)
    {
        if (!$this->followers->contains($follower)) {
            $this->followers->add($follower);
        }

        $follower->addFollowee($this);
    }

    public function serialize()
    {
        return serialize([
            $this->id,
            $this->email,
            $this->password,
        ]);
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->email,
            $this->password
            ) = unserialize($serialized);
    }
}
