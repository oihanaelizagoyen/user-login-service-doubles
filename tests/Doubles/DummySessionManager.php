<?php

namespace UserLoginService\Tests\Doubles;

use UserLoginService\Application\SessionManager;

class DummySessionManager implements SessionManager
{
    public function getSessions(): int
    {
        // TODO: Implement getSessions() method.
    }

    public function login(string $userName, string $password): bool
    {
        // TODO: Implement login() method.
    }
}