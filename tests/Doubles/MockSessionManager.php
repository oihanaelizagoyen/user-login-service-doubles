<?php

namespace UserLoginService\Tests\Doubles;

use Exception;
use UserLoginService\Application\SessionManager;
use function PHPUnit\Framework\throwException;

class MockSessionManager implements SessionManager
{

    private int $times = 0;
    private int $expectedTimes;
    private string $response;
    private string $argument;
    private string $expectedArgument;

    public function getSessions(): int
    {
        // TODO: Implement getSessions() method.
    }

    public function login(string $userName, string $password): bool
    {
        // TODO: Implement login() method.
    }

    public function logout(string $getUserName)
    {
        // TODO: Implement logout() method.
    }

    public function secureLogin(string $getUserName)
    {
        $this->argument = $getUserName;
        $this->times++;

        throw new Exception($this->response);
    }

    public function times(int $times)
    {
        $this->expectedTimes = $times;
    }

    public function withArguments(string $argument)
    {
        $this->expectedArgument = $argument;
    }

    public function andThrowException(string $errorMessage)
    {
        $this->response = $errorMessage;
    }

    public function verifyValid()
    {
        return $this->times == $this->expectedTimes && $this->argument === $this->expectedArgument;
    }
}