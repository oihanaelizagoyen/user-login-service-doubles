<?php

declare(strict_types=1);

namespace UserLoginService\Tests\Application;

use Exception;
use Mockery;
use PhpParser\Node\Scalar\EncapsedStringPart;
use PHPUnit\Framework\TestCase;
use UserLoginService\Application\SessionManager;
use UserLoginService\Application\UserLoginService;
use UserLoginService\Domain\User;
use UserLoginService\Tests\Doubles\DummySessionManager;
use UserLoginService\Tests\Doubles\FakeSessionManager;
use UserLoginService\Tests\Doubles\MockSessionManager;
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
    public function userIsLoggedInManualMockery()
    {
        $user = new User("user_name");
        $expectedLoggedUsers = [$user];
        $userLoginService = new UserLoginService(\Mockery::mock(SessionManager::class));

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
    //no se puede hacer con mockery
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

    /**
     * @test
     */
    public function UserNotSecurelyLoggedInIfUserNotExistsInExternalService()
    {
        $user = new User('user_name');
        $sessionManager = new MockSessionManager();
        $userLoginService = new UserLoginService($sessionManager);

        $sessionManager->times(1);
        $sessionManager->withArguments('user_name');
        $sessionManager->andThrowException('User does not exist');

        $secureLoginResponse = $userLoginService->secureLogin($user);

        $this->assertTrue($sessionManager->verifyValid());
        $this->assertEquals('Usuario no existe', $secureLoginResponse);
    }

    /**
     * @test
     */
    public function UserNotSecurelyLoggedInIfCredentialsIncorrect(){
        $user = new User('user_name');
        $sessionManager = new MockSessionManager();
        $userLoginService = new UserLoginService($sessionManager);

        $sessionManager->times(1);
        $sessionManager->withArguments('user_name');
        $sessionManager->andThrowException('User incorrect credentials');

        $secureLoginResponse = $userLoginService->secureLogin($user);

        $this->assertTrue($sessionManager->verifyValid());
        $this->assertEquals('Credenciales incorrectas', $secureLoginResponse);
    }

    /**
     * @test
     */
    public function UserNotSecurelyLoggedInExternalServiceNotResponding()
    {
        $user = new User('user_name');
        $sessionManager = new MockSessionManager();
        $userLoginService = new UserLoginService($sessionManager);

        $sessionManager->times(1);
        $sessionManager->withArguments("user_name");
        $sessionManager->andThrowException("Service not responding");

        $secureLoginResponse = $userLoginService->secureLogin($user);

        $this->assertTrue($sessionManager->verifyValid());
        $this->assertEquals("Servicio no responde", $secureLoginResponse);
    }


    /*public function userNotSecurelyLoggedInIfUserNotExistsInExternalServiceMockery()
    {
        $user = new User('user_name');
        $sessionManager = Mockery::mock(SessionManager::class);
        $userLoginService = new UserLoginService($sessionManager);

        $sessionManager
            ->shouldReceive('secureLogin')
            ->with('user_name')
            ->once()
            ->andThrow(new Exception("User does not exist"));

        $secureLoginResponse = $userLoginService->secureLogin($user);

        $this->assertEquals('Usuario no existe', $secureLoginResponse);
    }


    public function UserNotSecurelyLoggedInIfCredentialsIncorrectMockery()
    {
        $user = new User('user_name');
        $sessionManager = Mockery::mock(SessionManager::class);
        $userLoginService = new UserLoginService($sessionManager);

        $sessionManager
            ->shouldReceive('secureLogin')
            ->withArgs('user_name')
            ->once()
            ->andThrow(new Exception("User incorrect credentials"));

        $secureLoginResponse = $userLoginService->secureLogin($user);

        $this->assertEquals('Credenciales incorrectas', $secureLoginResponse);
    }


    public function UserNotSecurelyLoggedInExternalServiceNotRespondingMockery()
    {
        $user = new User('user_name');
        $sessionManager = Mockery::mock(SessionManager::class);
        $userLoginService = new UserLoginService($sessionManager);

        $sessionManager
            ->shouldReceive('secureLogin')
            ->withArgs('user_name')
            ->once()
            ->andThrow('Service not responding');

        $secureLoginResponse = $userLoginService->secureLogin($user);

        $this->assertEquals("Servicio no responde", $secureLoginResponse);
    }*/
}
