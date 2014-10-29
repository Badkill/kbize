<?php
namespace Kbize\Sdk;

use Kbize\Http\Client;
use Kbize\Sdk\Response\Login;
use Kbize\Sdk\Response\ProjectAndBoards;
use Kbize\Sdk\Response\AllTasks;
use Kbize\Sdk\Response\BoardStructure;

class HttpKbizeSdk implements KbizeSdk
{
    private $apikey;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->apikey = '';
    }

    public function setApiKey($apikey)
    {
        $this->apikey = $apikey;
    }

    public function login($email, $password)
    {
        $response = $this->client->post('login', [
            'email' => $email,
            'pass'  => $password
        ], [
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ]);

        return Login::fromArrayResponse($response->json());
    }

    public function getProjectsAndBoards()
    {
        $response = $this->client->post('get_projects_and_boards', [], [
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
            'apikey'       => $this->apikey,
        ]);

        return ProjectAndBoards::fromArrayResponse($response->json());
    }

    public function getBoardStructure($boardId)
    {
        throw new \Exception('Not implemented yet');
    }

    public function getFullBoardStructure($boardId)
    {
        $response = $this->client->post('get_full_board_structure', [
            'boardid' => $boardId,
        ], [
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
            'apikey'       => $this->apikey,
        ]);

        return BoardStructure::fromArrayResponse($response->json());
    }

    public function getBoardSettings($boardId)
    {
        throw new \Exception('Not implemented yet');
    }

    public function getBoardActivities($boardId, $fromDate, $toDate, array $parameters = array())
    {
        throw new \Exception('Not implemented yet');
    }

    public function createNewTask($boardId, array $parameters = array())
    {
        throw new \Exception('Not implemented yet');
    }

    public function deleteTask($boardId, $taskId)
    {
        throw new \Exception('Not implemented yet');
    }

    public function getTaskDetails($boardId, $taskId, array $parameters = array())
    {
        throw new \Exception('Not implemented yet');
    }

    public function getAllTasks($boardId, array $parameters = array())
    {
        $response = $this->client->post('get_all_tasks', [
            'boardid' => $boardId,
        ], [
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
            'apikey'       => $this->apikey,
        ]);

        return AllTasks::fromArrayResponse($response->json());
    }

    public function addComment($taskId, $comment)
    {
        throw new \Exception('Not implemented yet');
    }

    public function moveTask($boardId, $taskId, $column, array $parameters = array())
    {
        throw new \Exception('Not implemented yet');
    }

    public function editTask($boardId, $taskId, array $parameters = array())
    {
        throw new \Exception('Not implemented yet');
    }

    public function blockTask($boardId, $taskId, $event, $blockreason)
    {
        throw new \Exception('Not implemented yet');
    }

    public function addSubtask($taskParent, array $parameters = array())
    {
        throw new \Exception('Not implemented yet');
    }

    public function editSubtask($boardId, $subtaskId, array $parameters = array())
    {
        throw new \Exception('Not implemented yet');
    }

    //FIXME:!
    public function addHeader($key, $value)
    {
        $this->headers[$key] = $value;
        return $this;
    }
}
