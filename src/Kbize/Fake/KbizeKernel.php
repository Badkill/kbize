<?php
namespace Kbize\Fake;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Dumper;

use Kbize\Collection\Tasks;

class KbizeKernel implements \Kbize\KbizeKernel
{
    public function __construct()
    {

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
        $yamlParser = new Parser();
        return Tasks::fromArray($yamlParser->parse(file_get_contents('fixtures/tasks.yml')));
    }
}
