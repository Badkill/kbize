<?php
namespace Kbize\Sdk\Response;

class ProjectAndBoards
{
    /**
     *
     * {
     *   "projects":[
     *     {"name":"Project","id":"1","boards":[
     *       {"name":"Service\/Merchant Integrations","id":"4"},
     *       {"name":"Tech Operations","id":"3"},
     *       {"name":"Main development","id":"2"}
     *     ]}
     *   ]
     * }
     */
    public static function fromArrayResponse(array $response)
    {
        return new self($response['projects']);
    }

    private function __construct(array $data)
    {
        $this->data = $data;
    }

    public function projects()
    {
        $projects = [];
        foreach($this->data as $project) {
            $projects[] = [
                'name' => $project['name'],
                'id' => $project['id'],
            ];
        }

        return $projects;
    }

    public function boards($projectId)
    {
        $projects = [];
        foreach($this->data as $project) {
            if ($project['id'] == $projectId) {
                return $project['boards'];
            }
        }

        throw new \Exception("Project: `$projectId` does not exists"); //TODO:! custom exception
    }
}
