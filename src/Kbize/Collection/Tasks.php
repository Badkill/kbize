<?php
namespace Kbize\Collection;

use Kbize\Collection\Matcher\AndMatcherStrategy;
use Kbize\Collection\Matcher\OrMatcherStrategy;

class Tasks implements TasksInterface
{
    private $tasks;

    private function __construct(array $tasks)
    {
        $this->tasks = $tasks;
    }

    public static function fromArray(array $tasks = [])
    {
        return new self($tasks);
    }

    public function filter(array $filters = [], $useAnd = true)
    {
        $collectedTasks = [];

        if ($useAnd) {
            $strategy = new AndMatcherStrategy();
        } else {
            $strategy = new OrMatcherStrategy();
        }

        foreach ($this->tasks as $task) {
            if ($strategy->match($task, $filters)) {
                $collectedTasks[] = $task;
            }
        }

        return self::fromArray($collectedTasks);
    }

    public function slice($from, $to)
    {
        return new self(array_slice($this->tasks, $from, $to));
    }

    public function count()
    {
        return count($this->tasks);
    }

    //FIXME:! Iterator doesn't work in phpunit :(
    public function tasks()
    {
        return $this->tasks;
    }
}
