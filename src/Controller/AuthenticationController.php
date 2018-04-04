<?php

namespace App\Controller;

class AuthenticationController
{
    public function getTokenAction()
    {
        // The security layer will intercept this request
        return new Response('', 401);
    }
}
