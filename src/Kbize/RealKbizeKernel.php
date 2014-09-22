<?php
namespace Kbize;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Dumper;

use Kbize\Collection\Tasks;
use Kbize\Exception\ForbiddenException;
use Kbize\Sdk\KbizeSdk;

class RealKbizeKernel implements KbizeKernel
{
    private $isAuthenticated = false;

    public function __construct(KbizeSdk $sdk)
    {
        $this->sdk = $sdk;
    }

    public function authenticate($email, $password)
    {
        $loginResponse = $this->sdk->login($email, $password);
        $this->sdk->setApiKey($loginResponse->apikey());
        return $loginResponse;
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
