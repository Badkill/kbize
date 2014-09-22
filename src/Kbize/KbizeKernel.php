<?php
namespace Kbize;

interface KbizeKernel
{
    public function authenticate($username, $password);

    public function getProjects();

    public function getBoards($projectId);

    public function getAllTasks($boardId);
}
