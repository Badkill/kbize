<?php
namespace Kbize;

interface KbizeKernel
{
    public function getProjects();

    public function getBoards($projectId);

    public function getAllTasks($boardId);
}
