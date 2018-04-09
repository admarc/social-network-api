<?php

namespace App\Controller;

use App\Entity\User;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

abstract class RestController
{
    protected $tokenStorage;
    protected $serializer;

    protected function createJsonResponse(int $status, string $body = '', array $headers = []): Response
    {
        $headers = array_merge($headers, ['content-type' => 'application/json']);

        return new Response($body, $status, $headers);
    }

    protected function getUser(): User
    {
        return $this->tokenStorage->getToken()->getUser();
    }

    public function setTokenStorage(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function setSerializer(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }
}
