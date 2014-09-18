<?php
namespace Test\Kbize\Console\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Kbize\Console\Command\KbizeCommand;
use Kbize\KbizeKernel;
use Kbize\Console\MissingMandatoryParametersRequest;

class KbizeCommandTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->kernel = $this->getMock('Kbize\KbizeKernel');
        $this->sampleCommand = new SampleCommand($this->kernel);
        $this->application = new Application();
        $this->application->add($this->sampleCommand);
    }

    public function testAskForMandatoryOptionsIfThemAreMissing()
    {
        $projectId = 1;

        $command = $this->application->find('sample:command');
        $dialog = $command->getHelper('question');
        $dialog->setInputStream($this->getInputStream('foo'));

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
        ]);

        $this->assertRegExp("/foo.*bar\n.*bar.*foo/", $commandTester->getDisplay());
    }

    protected function getInputStream($input)
    {
        $stream = fopen('php://memory', 'r+', false);
        fputs($stream, $input);
        rewind($stream);

        return $stream;
    }
}

class SampleCommand extends KbizeCommand
{
    public function __construct(KbizeKernel $kernel)
    {
        parent::__construct($kernel);

        $this->missingParameterRequest = new MissingMandatoryParametersRequest([
            'option' => function () {
                return [
                    'foo' => 'bar',
                    'bar' => 'foo',
                ];
            },
        ]);
    }

    protected function configure()
    {
        $this->setName('sample:command')
            ->addOption(
                'option',
                'o',
                InputOption::VALUE_OPTIONAL,
                'sample option'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}
