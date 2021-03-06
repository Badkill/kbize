<?php
namespace Kbize;

interface KbizeKernel
{
    public function isAuthenticated();

    public function authenticate($email, $password);

    public function getProjects();

    public function getBoards($projectId);

    /**
     * @return Collection\Tasks
     */
    public function getAllTasks($boardId);
}
