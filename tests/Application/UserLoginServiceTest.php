<?php

declare(strict_types=1);

namespace UserLoginService\Tests\Application;

use PHPUnit\Framework\TestCase;
use UserLoginService\Application\UserLoginService;
use UserLoginService\Domain\User;
use UserLoginService\Infrastructure\FacebookSessionManager;
use UserLoginService\Tests\Doubles\DummySessionManager;
use UserLoginService\Tests\Doubles\FakeSessionManager;
use UserLoginService\Tests\Doubles\SpySessionManager;
use UserLoginService\Tests\Doubles\StubSessionManager;

final class UserLoginServiceTest extends TestCase
{
    /**
     * @test
     */
    public function userIsLoggedInManual()
    {
        $user = new User("user_name");
        $expectedLoggedUsers = [$user];
        $userLoginService = new UserLoginService(new DummySessionManager());

        $userLoginService->manualLogin($user);

        $this->assertEquals($expectedLoggedUsers, $userLoginService->getLoggedUsers());
    }

    /**
     * @test
     */
    public function thereIsNoLoggedUsers()
    {
        $userLoginService = new UserLoginService(new DummySessionManager());


        $this->assertEmpty($userLoginService->getLoggedUsers());
    }

    /**
     * @test
     */
    public function countExternalSessions()
    {

        $userLoginService = new UserLoginService(new StubSessionManager());

        $externalSessions = $userLoginService->countExternalSessions();

        $this->assertEquals(10, $externalSessions);
    }

    /**
     * @test
     */
    public function userIsLoggedInExternalService()
    {

        $userName = "user_name";
        $password = "password";
        $userLoginService = new UserLoginService(new StubSessionManager());


        $result = $userLoginService->login($userName, $password);

        $this->assertEquals($userLoginService::LOGIN_CORRECTO, $result);

    }

    /**
     * @test
     */
    public function userIsNotLoggedInExternalService()
    {

        $userName = "user_name";
        $password = "wrong_password";
        $userLoginService = new UserLoginService(new FakeSessionManager());


        $result = $userLoginService->login($userName, $password);

        $this->assertEquals($userLoginService::LOGIN_INCORRECTO, $result);

    }

    /**
     * @test
     */
    public function userNotLoggedOutUserNotBeingLoggedIn()
    {

        $user = new User("user_name");
        $userLoginService = new UserLoginService(new DummySessionManager());

        $result = $userLoginService->logout($user);

        $this->assertEquals($userLoginService::USUARIO_NO_LOGEADO, $result);
    }

    /**
     * @test
     */
    public function userLogout()
    {

        $user = new User("user_name");
        $sessionManager = new SpySessionManager();
        $userLoginService = new UserLoginService($sessionManager);
        $userLoginService->manualLogin($user);

        $result = $userLoginService->logout($user);

        $sessionManager->verifyLogoutCalls(1);
        $this->assertEquals("Ok", $result);
    }
}
