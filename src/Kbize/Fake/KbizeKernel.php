<?php
namespace Kbize\Fake;

use Symfony\Component\Yaml\Parser;
use Kbize\Collection\Tasks;
use Kbize\Exception\ForbiddenException;

class KbizeKernel implements \Kbize\KbizeKernel
{
    private $isAuthenticated = false;

    public function __construct()
    {

    }

    public function authenticate($email, $password)
    {
        if ("user@email.com" === $email && "secret" === $password) {
            $this->isAuthenticated = true;
            return true;
        }

        throw new ForbiddenException();
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
        if (!$this->isAuthenticated) {
            throw new ForbiddenException();
        }

        $yamlParser = new Parser();
        return Tasks::fromArray($yamlParser->parse(file_get_contents('fixtures/tasks.yml')));
    }
}
