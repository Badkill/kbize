<?php
namespace Test\Kbize\Console\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Kbize\Console\Command\KbizeCommand;
use Kbize\KbizeKernel;
use Kbize\KbizeKernelFactory;
use Kbize\Console\MissingMandatoryParametersRequest;

class KbizeCommandTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->kernel = $this->getMock('Kbize\KbizeKernel');
        $this->kernelFactory = $this->getMock('Kbize\KbizeKernelFactory');
        $this->kernelFactory->expects($this->any())
            ->method('forProfile')
            ->will($this->returnValue($this->kernel))
        ;
        $this->sampleCommand = new SampleCommand($this->kernelFactory);
        $this->application = new Application();
        $this->application->add($this->sampleCommand);
    }

    public function testAskForMandatoryOptionsIfThemAreMissing()
    {
        $projectId = 1;

        $this->kernel->expects($this->at(0))
            ->method('isAuthenticated')
            ->will($this->returnValue(true))
        ;

        $command = $this->application->find('sample:command');
        $dialog = $command->getHelper('question');
        $dialog->setInputStream($this->getInputStream("foo"));

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
        ]);

        $this->assertRegExp("/foo.*bar\n.*bar.*foo/", $commandTester->getDisplay());
    }

    public function testAskForUsernameAndPasswordInCaseOfForbiddenException()
    {
        $username = 'username@email.com';
        $password = 'password';

        $this->kernel->expects($this->any())
            ->method('getAllTasks')
            ->will($this->throwException(new \Kbize\Exception\ForbiddenException()))
        ;

        $this->kernel->expects($this->any())
            ->method('authenticate')
            ->with($username, $password)
        ;

        $command = $this->application->find('sample:command');

        $dialog = $command->getHelper('question');
        $dialog->setInputStream($this->getInputStream("
            $username\n$password\n
            $username\n$password\n
            $username\n$password\n
            $username\n$password\n
            $username\n$password
        ")); //FIXME:!!

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            '--option' => 'foo',
        ]);
    }

    public function testRepeatDoExecuteAfterSuccessfulAuthentication()
    {
        $username = 'username@email.com';
        $password = 'password';

        $this->kernel->expects($this->at(0))
            ->method('isAuthenticated')
            ->will($this->returnValue(true))
        ;

        $this->kernel->expects($this->at(1))
            ->method('getAllTasks')
            ->will($this->throwException(new \Kbize\Exception\ForbiddenException()))
        ;

        $this->kernel->expects($this->at(2))
            ->method('authenticate')
            ->with($username, $password)
        ;

        $this->kernel->expects($this->at(3))
            ->method('getAllTasks')
            ->will($this->returnValue([]))
        ;

        $command = $this->application->find('sample:command');

        $dialog = $command->getHelper('question');
        $dialog->setInputStream($this->getInputStream("$username\n$password"));

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            '--option' => 'foo',
        ]);
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
    public function __construct(KbizeKernelFactory $kernel)
    {
        parent::__construct($kernel);
    }

    protected function configure()
    {
        parent::configure();

        $this->setName('sample:command')
            ->addOption(
                'option',
                'o',
                InputOption::VALUE_OPTIONAL,
                'sample option'
            )
        ;
    }

    protected function missingParameterRequestSetUp()
    {
        $this->missingParameterRequest = new MissingMandatoryParametersRequest([
            'option' => function () {
                return [
                    'foo' => 'bar',
                    'bar' => 'foo',
                ];
            },
        ]);
    }

    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $taskCollection = $this->kernel
            ->getAllTasks(1)
        ;
    }
}
