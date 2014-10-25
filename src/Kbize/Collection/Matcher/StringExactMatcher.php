<?php
namespace Kbize\Collection\Matcher;

class StringExactMatcher implements StringMatcher
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function match($compared)
    {
        return $compared == $this->value;
    }
}
