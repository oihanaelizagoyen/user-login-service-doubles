<?php

namespace UserLoginService\Application;

use UserLoginService\Domain\User;

class UserLoginService
{
    private array $loggedUsers = [];

    public function manualLogin(User $user): void
    {
        $this->loggedUsers[] = $user;
    }

    /**
     * @return array
     */
    public function getLoggedUsers(): array
    {
        return $this->loggedUsers;
    }

}