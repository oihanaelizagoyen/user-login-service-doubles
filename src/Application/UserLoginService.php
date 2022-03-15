<?php

namespace UserLoginService\Application;

use Exception;
use UserLoginService\Domain\User;

class UserLoginService
{
    const LOGIN_CORRECTO = "Login correcto";
    const LOGIN_INCORRECTO = "Login incorrecto";
    const USUARIO_NO_LOGEADO = "Usuario no logeado";

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

    public function countExternalSessions():int
    {

        return $this->sessionManager->getSessions();
    }

    public function login(string $userName, string $password): string
    {

        if($this->sessionManager->login($userName, $password)){
            return self::LOGIN_CORRECTO;
        }

        return self::LOGIN_INCORRECTO;
    }

    public function logout(User $user): string
    {

        if(!in_array($user, $this->loggedUsers)){
            return self::USUARIO_NO_LOGEADO;
        }

        $this->sessionManager->logout($user->getUserName());

        return "Ok";
    }

    public function secureLogin(User $user) : String
    {
        try{

            $this->sessionManager->secureLogin($user->getUserName());

        }catch (Exception $exception){

            if($exception->getMessage() == "User does not exist"){
                return "Usuario no existe";
            }
            if($exception->getMessage() == "User incorrect credentials"){
                return "Credenciales incorrectas";
            }
            if($exception->getMessage() == "Service not responding"){
                return "Servicio no responde";
            }

        }

        return "Ok";
    }

}