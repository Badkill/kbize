<?php
namespace Kbize\Collection;

interface TasksInterface// extends \Iterator
{
    /**
     * @return Tasks
     */
    public function filter(array $filters = [], $useAnd = true);

    /**
     * @return Tasks
     */
    public function slice($from, $to);

    /**
     * @return integer
     */
    public function count();

    //FIXME:! Iterator doesn't work in phpunit :(
    public function tasks();
}
