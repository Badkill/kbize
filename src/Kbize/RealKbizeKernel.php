<?php
namespace Kbize;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Dumper;

use Kbize\Collection\Tasks;
use Kbize\Exception\ForbiddenException;
use Kbize\Sdk\KbizeSdk;

class RealKbizeKernel implements KbizeKernel
{
    private $sdk;
    private $user;

    public function __construct(KbizeSdk $sdk, User $user)
    {
        $this->sdk = $sdk;
        $this->user = $user;
        $this->sdk->setApiKey($this->user->apikey());
    }

    public function isAuthenticated()
    {
        return $this->user->isAuthenticated();
    }

    public function authenticate($email, $password)
    {
        $loginResponse = $this->sdk->login($email, $password);
        $this->user->update($loginResponse->data());
        $this->sdk->setApiKey($this->user->apikey());

        return $this->user;
    }

    public function getProjects()
    {
        return [
            '1' => 'first project',
        ];
    }

    public function getBoards($projectId)
    {
        return [
            '1' => 'first board',
            '2' => 'second board',
        ];
    }

    public function getAllTasks($boardId)
    {
        $allTaskResponse = $this->sdk->getAllTasks($boardId);
        return Tasks::fromArray($allTaskResponse->toArray());
    }
}
