<?php
namespace Kbize\Collection\Matcher;

abstract class MatcherStrategy
{
    abstract public function match($collection, $filters);

    protected function collectionMatch($collection, $filter)
    {
        $keyValueFilter = $this->keyValueFilter($filter);

        foreach ($collection as $field => $fieldValue) {
            if ($keyValueFilter['key'] && $field != $keyValueFilter['key']) {
                continue;
            }

            if (is_array($fieldValue)) {
                if ($this->collectionMatch($fieldValue, $filter)) {
                    return true;
                }

                continue;
            }

            if (is_object($fieldValue)) {
                if (is_callable([$fieldValue, '__toString'])) {
                    $fieldValue = $fieldValue->__toString();
                } else {
                    continue;
                }
            }

            $stringMatcherClass = 'partial' == $keyValueFilter['type'] ?
                'Kbize\Collection\Matcher\StringPartialMatcher' :
                'Kbize\Collection\Matcher\StringExactMatcher'
            ;

            $stringMatcher = new $stringMatcherClass(strtolower($fieldValue));
            if ($stringMatcher->match(strtolower($keyValueFilter['value']))) {
                return true;
            }
        }
    }

    private function keyValueFilter($filter)
    {
        if (($pos = strpos($filter, '=')) != false) { //priority=high
            return [
                'key' => substr($filter, 0, $pos),
                'value' => substr($filter, $pos + 1),
                'type' => 'partial',
            ];
        } elseif (0 === strpos($filter, '=')) { // =27
            return [
                'key' => 'taskid',
                'value' => substr($filter, 1),
                'type' => 'exact',
            ];
        } elseif (0 === strpos($filter, '@')) { // @name.surname
            return [
                'key' => 'assignee',
                'value' => substr($filter, 1),
                'type' => 'partial',
            ];
        }

        return [
            'key' => null,
            'value' => $filter,
            'type' => 'partial',
        ];
    }
}
