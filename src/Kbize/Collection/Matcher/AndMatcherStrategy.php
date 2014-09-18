<?php
namespace Kbize\Collection\Matcher;

class AndMatcherStrategy extends MatcherStrategy
{
    public function match($collection, $filters)
    {
        $theCollectionMatch = true;

        foreach ($filters as $filter) {
            $theCollectionMatch &= $this->collectionMatch($collection, $filter);
        }

        return $theCollectionMatch;
    }
}
