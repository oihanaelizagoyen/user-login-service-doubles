<?php

namespace UserLoginService\Application;

use UserLoginService\Domain\User;

class UserLoginService
{
    const LOGIN_CORRECTO = "Login correcto";
    const LOGIN_INCORRECTO = "Login incorrecto";
    private array $loggedUsers = [];
    private SessionManager $sessionManager;

    public function __construct (SessionManager $sessionManager){
        $this->sessionManager = $sessionManager;
    }

    public function manualLogin(User $user): void
    {
        $this->loggedUsers[] = $user;
    }

    public function getLoggedUsers(): array
    {
        return $this->loggedUsers;
    }

    public function countExternalSessions():int{

        return $this->sessionManager->getSessions();
    }

    public function login(String $userName, String $password): String{

        if($this->sessionManager->login($userName, $password)){
            return self::LOGIN_CORRECTO;
        }

        return self::LOGIN_INCORRECTO;
    }

}