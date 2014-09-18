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

            if (strpos(strtolower($fieldValue), strtolower($keyValueFilter['value'])) !== false) {
                return true;
            }
        }
    }

    private function keyValueFilter($filter)
    {
        if (($pos = strpos($filter, '=')) !== false) {
            return [
                'key' => substr($filter, 0, $pos),
                'value' => substr($filter, $pos + 1)
            ];
        } elseif (0 === strpos($filter, '@')) {
            return [
                'key' => 'taskid',
                'value' => substr($filter, 1)
            ];
        }

        return [
            'key' => null,
            'value' => $filter
        ];
    }
}
