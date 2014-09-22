<?php
namespace Test\Unit\Kbize\Sdk;

use Kbize\Http\Client;
use Kbize\Sdk\HttpKbizeSdk;

class HttpKbizeSdkTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->client = $this->getMock('Kbize\Http\Client');
        $this->sdk = new HttpKbizeSdk($this->client);
    }

    public function testLoginCallWorksRightAndReturnsALoginResponse()
    {
        $email = 'name.surname@email.com';
        $password = 'secret';

        $response = $this->getMock('Kbize\Http\Response');
        $response->expects($this->once())
            ->method('json')
            ->will($this->returnValue([
                'email'       => 'name.surname@email.com',
                'username'    => 'name.surname',
                'realname'    => 'Name Surname',
                'companyname' => 'Company',
                'timezone'    => '0:0',
                'apikey'      => 'ERtVj8IJKn9jUkSyY0ml6HMK3c1N4tVZSjHSmQVy',
            ]));

        $this->client->expects($this->once())
            ->method('post')
            ->with('/login', [
                'email' => $email,
                'pass'  => $password
            ], [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ])
            ->will($this->returnValue($response))
        ;

        $this->assertInstanceOf(
            'Kbize\Sdk\Response\Login',
            $this->sdk->login($email, $password)
        );
    }

    public function testProjectsAndBoardsCallWorksRightAndReturnsAProjectAndBoardsResponse()
    {
        $response = $this->getMock('Kbize\Http\Response');
        $response->expects($this->once())
            ->method('json')
            ->will($this->returnValue([
                'projects' => [
                    0 =>[
                        'name' => 'Company',
                        'id' => '1',
                        'boards' => [
                            0 => [
                                'name' => 'Main development',
                                'id' => '2',
                            ],
                            1 => [
                                'name' => 'Support board',
                                'id' => '3',
                            ],
                        ],
                    ],
                ],
            ]));

        $this->sdk->setApiKey('ERtVj8IJKn9jUkSyY0ml6HMK3c1N4tVZSjHSmQVy');

        $this->client->expects($this->once())
            ->method('post')
            ->with('/get_projects_and_boards', [], [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
                'apikey'       => 'ERtVj8IJKn9jUkSyY0ml6HMK3c1N4tVZSjHSmQVy',
            ])
            ->will($this->returnValue($response))
        ;

        $this->assertInstanceOf(
            'Kbize\Sdk\Response\ProjectAndBoards',
            $this->sdk->getProjectsAndBoards()
        );
    }

    public function testAllTasksCallWorksRightAndReturnsAAllTasksResponse()
    {
        $boardId = 1;
        $response = $this->getMock('Kbize\Http\Response');
        $response->expects($this->once())
            ->method('json')
            ->will($this->returnValue([]));

        $this->sdk->setApiKey('ERtVj8IJKn9jUkSyY0ml6HMK3c1N4tVZSjHSmQVy');

        $this->client->expects($this->once())
            ->method('post')
            ->with('/get_all_tasks', [
                'boardid' => $boardId,
            ], [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
                'apikey'       => 'ERtVj8IJKn9jUkSyY0ml6HMK3c1N4tVZSjHSmQVy',
            ])
            ->will($this->returnValue($response))
        ;

        $this->assertInstanceOf(
            'Kbize\Sdk\Response\AllTasks',
            $this->sdk->getAllTasks($boardId)
        );
    }
}
