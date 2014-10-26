<?php
namespace Test\Kbize\Console\Command;

abstract class KbizeComandBaseTest extends \PHPUnit_Framework_TestCase
{
    protected function kernelFactoryReturns($kernel)
    {
        $this->kernelFactory = $this->getMockBuilder('Kbize\KbizeKernelFactory')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->kernelFactory->expects($this->any())
            ->method('forProfile')
            ->will($this->returnValue($kernel))
        ;
    }

    protected function userIsAuthenticated($authenticated = true)
    {
        $this->kernel->expects($this->once())
            ->method('isAuthenticated')
            ->will($this->returnValue($authenticated));
        ;
    }

    /* protected function thereAreSomeBoards(array $boards = []) */
    /* { */
    /*     if (!$boards) { */
    /*         $boards = [ */
    /*             0 => [ */
    /*                 'name' => 'Main development', */
    /*                 'id' => '2', */
    /*             ], */
    /*             1 => [ */
    /*                 'name' => 'Support board', */
    /*                 'id' => '3', */
    /*             ], */
    /*         ]; */
    /*     } */

    /*     $this->kernel->expects($this->once()) */
    /*         ->method('getBoards') */
    /*         ->with(1) */
    /*         ->will($this->returnValue($boards)) */
    /*     ; */
    /* } */

    protected function getInputStream($input)
    {
        $stream = fopen('php://memory', 'r+', false);
        fputs($stream, $input);
        rewind($stream);

        return $stream;
    }
}
