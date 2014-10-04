<?php
namespace Test\Kbize;

use Kbize\StateUser;

class StateUserTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->configRepository = $this->getMock('Kbize\Config\ConfigRepository');
    }

    public function testUserIsNotAuthenticatedByDefault()
    {
        $this->user = new StateUser($this->configRepository);
        $this->assertFalse($this->user->isAuthenticated());
    }

    public function testUserIsAuthenticatedIfAnApikeyIsStored()
    {
        $this->configRepository->expects($this->any())
            ->method('toArray')
            ->will($this->returnValue($this->sampleUserData()))
        ;

        $this->user = new StateUser($this->configRepository);

        $this->assertTrue($this->user->isAuthenticated());
    }

    public function testCallConfigDestroyOnLogout()
    {
        $this->user = new StateUser($this->configRepository);

        $this->configRepository->expects($this->once())
            ->method('destroy')
        ;

        $this->user->logout();
    }

    public function testStoreUserDataOnUpdate()
    {
        $userData = $this->sampleUserData();

        $this->user = new StateUser($this->configRepository);

        $this->configRepository->expects($this->once())
            ->method('replace')
            ->with($userData)
            ->will($this->returnSelf())
        ;

        $this->configRepository->expects($this->once())
            ->method('store')
        ;

        $this->user->update($userData);
    }

    private function sampleUserData()
    {
        return [
            "email" => "name.surname@email.com",
            "username" => "name.surname",
            "realname" => "'Name Surname'",
            "companyname" => "Company",
            "timezone" => "'0:0'",
            "apikey" => "ERtVj8IJKn9jUkSyY0ml6HMK3c1N4tVZSjHSmQVy",
        ];
    }
}
