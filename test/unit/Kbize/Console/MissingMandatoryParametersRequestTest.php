<?php
namespace Test\Kbize\Console;

use Symfony\Component\Console\Question\ChoiceQuestion;
use Kbize\Console\MissingMandatoryParametersRequest;

class MissingMandatoryParametersRequestTest extends \PHPUnit_Framework_TestCase
{
    public function __construct()
    {
        $this->input = $this->getMock('Symfony\Component\Console\Input\InputInterface');
        $this->output = $this->getMock('Symfony\Component\Console\Output\OutputInterface');
        $this->questionHelper = $this->getMock('Symfony\Component\Console\Helper\QuestionHelper');
    }

    public function testAskForMandatoryOptionIfItIsMissing()
    {
        $firstOptionId = 'key1';
        $availableValues = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $question = (new ChoiceQuestion('Choose a first', $availableValues, 'key1'))
            ->setErrorMessage('The first `%s` is not valid.')
            ->setMaxAttempts(5)
        ;

        $this->questionHelper->expects($this->once())
            ->method('ask')
            ->with($this->input, $this->output, $question)
            ->will($this->returnValue('value1'))
        ;

        $this->input->expects($this->at(0))
            ->method('getOption')
            ->with('first')
            ->will($this->returnValue(null))
        ;

        $this->input->expects($this->at(1))
            ->method('setOption')
            ->with('first', $firstOptionId)
        ;

        $missingParameterRequest = new MissingMandatoryParametersRequest([
            'first' => function () use ($availableValues) {
                return $availableValues;
            },
        ]);

        $missingParameterRequest->enrichInputs($this->input, $this->output, $this->questionHelper);
    }

    public function testAskForMandatoryOptionsIfThemAreMissing()
    {
        $firstOptionId = 'key1';
        $secondOptionId = 'key2';

        $availableValues = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $this->questionHelper->expects($this->at(0))
            ->method('ask')
            ->will($this->returnValue('value1'))
        ;

        $this->questionHelper->expects($this->at(1))
            ->method('ask')
            ->will($this->returnValue('value2'))
        ;

        $this->input->expects($this->at(1))
            ->method('setOption')
            ->with('first', $firstOptionId)
        ;

        $this->input->expects($this->at(3))
            ->method('setOption')
            ->with('second', $secondOptionId)
        ;

        $missingParameterRequest = new MissingMandatoryParametersRequest([
            'first' => function () use ($availableValues) {
                return $availableValues;
            },
            'second' => function () use ($availableValues) {
                return $availableValues;
            },
        ]);

        $missingParameterRequest->enrichInputs($this->input, $this->output, $this->questionHelper);
    }

    public function testDoNotAskForMandatoryOptionIfItIsMissingButOnlyOneValueIsValid()
    {
        $availableValues = [
            'key1' => 'value1',
        ];

        $this->questionHelper->expects($this->never())
            ->method('ask')
        ;

        $this->input->expects($this->at(0))
            ->method('getOption')
            ->with('first')
            ->will($this->returnValue(null))
        ;

        $this->input->expects($this->at(1))
            ->method('setOption')
            ->with('first', 'key1')
        ;

        $missingParameterRequest = new MissingMandatoryParametersRequest([
            'first' => function () use ($availableValues) {
                return $availableValues;
            },
        ]);

        $missingParameterRequest->enrichInputs($this->input, $this->output, $this->questionHelper);
    }

    public function testDoNotAskForMandatoryOptionIfItIsMissingButNoValuesAreAvailable()
    {
        $availableValues = [];

        $this->questionHelper->expects($this->never())
            ->method('ask')
        ;

        $this->input->expects($this->at(0))
            ->method('getOption')
            ->with('first')
            ->will($this->returnValue(null))
        ;

        $this->input->expects($this->never())
            ->method('setOption')
        ;

        $missingParameterRequest = new MissingMandatoryParametersRequest([
            'first' => function () use ($availableValues) {
                return $availableValues;
            },
        ]);

        $missingParameterRequest->enrichInputs($this->input, $this->output, $this->questionHelper);
    }
}
