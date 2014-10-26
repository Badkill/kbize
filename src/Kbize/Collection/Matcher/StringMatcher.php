<?php
namespace Kbize\Collection\Matcher;

interface StringMatcher
{
    /**
     * @return boolean
     */
    public function match($value);
}
