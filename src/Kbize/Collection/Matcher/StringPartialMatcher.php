<?php
namespace Kbize\Collection\Matcher;

class StringPartialMatcher implements StringMatcher
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function match($compared)
    {
        return strpos($this->value, $compared) !== false;
    }
}
