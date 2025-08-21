<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (in_array('IS_LOCKED', $user->getRoles())) {
            throw new CustomUserMessageAccountStatusException('Votre compte est bloqué.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // Rien à faire ici
    }
}