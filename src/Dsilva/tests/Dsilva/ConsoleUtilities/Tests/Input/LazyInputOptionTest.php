<?php
namespace Dsilva\ConsoleUtilities\Tests\Input;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Output\OutputInterface;
use Dsilva\ConsoleUtilities\Command\MandatoryOptionsCommand;
use Dsilva\ConsoleUtilities\Input\LazyInputOption;

class LazyInputOptionTest extends \PHPUnit_Framework_TestCase
{
    public function testCreationWithMandatoryModeDontCauseAnError()
    {
        $option = new LazyInputOption(
            'option',
            'o',
            InputOption::VALUE_OPTIONAL | LazyInputOption::OPTION_IS_LAZY
        );

        $this->assertTrue($option->isMandatory());
    }

    public function testAvailableValuesExecuteCallback()
    {
        $availableValues = [
            'foo' => 'bar'
        ];

        $option = new LazyInputOption(
            'option',
            'o',
            InputOption::VALUE_OPTIONAL | LazyInputOption::OPTION_IS_LAZY,
            'sample option',
            null,
            function () use ($availableValues) {
                return $availableValues;
            }
        );

        $input = $this->getMock('Symfony\Component\Console\Input\InputInterface');
        $this->assertEquals($availableValues, $option->availableValues($input));
    }

    public function testAvailableValuesRetunrsLazyValueIfItIsNotCallable()
    {
        $availableValues = [
            'foo' => 'bar'
        ];

        $option = new LazyInputOption(
            'option',
            'o',
            InputOption::VALUE_OPTIONAL | LazyInputOption::OPTION_IS_LAZY,
            'sample option',
            null,
            $availableValues
        );

        $input = $this->getMock('Symfony\Component\Console\Input\InputInterface');
        $this->assertEquals($availableValues, $option->availableValues($input));
    }
}
