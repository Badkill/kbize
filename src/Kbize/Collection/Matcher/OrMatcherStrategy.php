<?php
namespace Kbize\Collection\Matcher;

class OrMatcherStrategy extends MatcherStrategy
{
    public function match($collection, $filters)
    {
        $theCollectionMatch = false;

        foreach ($filters as $filter) {
            $theCollectionMatch |= $this->collectionMatch($collection, $filter);
        }

        return $theCollectionMatch;
    }
}
