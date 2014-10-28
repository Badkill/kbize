<?php
namespace Test\Kbize\Console\Command;

use LazyOptionCommand\Input\LazyInputOption;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Kbize\Console\Command\KbizeCommand;
use Kbize\KbizeKernel;
use Kbize\KbizeKernelFactory;
use Kbize\Console\MissingMandatoryParametersRequest;

class KbizeCommandTest extends KbizeComandBaseTest
{
    public function setUp()
    {
        $this->kernel = $this->getMock('Kbize\KbizeKernel');
        $this->kernelFactoryReturns($this->kernel);
        $this->sampleCommand = new SampleCommand($this->kernelFactory, []);
        $this->application = new Application();
        $this->application->add($this->sampleCommand);
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
}

class SampleCommand extends KbizeCommand
{
    public function __construct(KbizeKernelFactory $kernel, array $settings = [])
    {
        parent::__construct($kernel, $settings);
    }

    protected function configure()
    {
        parent::configure();

        $this->setName('sample:command')
            ->addRawOption(new LazyInputOption(
                'option',
                'o',
                InputOption::VALUE_OPTIONAL,
                'sample option',
                null,
                function () {
                    return [
                        'foo' => 'bar',
                        'bar' => 'foo',
                    ];
                }
            ))
        ;
    }

    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $input->getOption('option');
        $taskCollection = $this->kernel
            ->getAllTasks(1)
        ;
    }
}
