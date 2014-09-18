<?php
namespace Kbize\Collection;

interface TasksInterface// extends \Iterator
{
    public function filter(array $filters = [], $useAnd = true);

    public function slice($from, $to);

    public function count();

    //FIXME:! Iterator doesn't work in phpunit :(
    public function tasks();
}
