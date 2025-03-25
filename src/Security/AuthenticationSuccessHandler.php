<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class AuthenticationSuccessHandler
{
    public function __invoke(AuthenticationSuccessEvent $event): void
    {
        $data = $event->getData();

        $data['access'] = $data['token'];
        unset($data['token']);

        $event->setData($data);
    }
}
