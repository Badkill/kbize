<?php

use Kbize\Console\String;

class StringTest extends \PHPUnit_Framework_TestCase
{
    public function testCutStringIfItIsWiderThanDesiredSize()
    {
        $string = new String('test');
        $this->assertEquals('te', $string->fixed(2));
    }

    public function testAddPadCharsIfStringIsLessWideThanDesiredSize()
    {
        $string = new String('test');
        $this->assertEquals('test      ', $string->fixed(10));
    }

    public function testColorTheStringIfColorIsPassed()
    {
        $string = new String('test');
        $this->assertEquals('<fg=red>test</fg=red>', $string->color('red')->__toString());
    }

    public function testReturnOriginalStringIfColorIsNull()
    {
        $string = new String('test');
        $this->assertEquals('test', $string->color(null));
    }

    public function testReturnsNewBoldStringWhenBoldMethodIsCalled()
    {
        $string = new String('test');
        $this->assertEquals('<options=bold>test</options=bold>', $string->bold()->__toString());
    }

    public function testReturnsNewBoldAndColoredStringWhenBoldAndColorMethodsAreCalled()
    {
        $string = new String('test');
        $this->assertEquals(
            '<fg=red;options=bold>test</fg=red;options=bold>',
            $string->color('red')->bold()->__toString()
        );
    }
}
