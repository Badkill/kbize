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

    //FIXME:! move it out
    public function testAskForMandatoryOptionsIfThemAreMissing()
    {
        $projectId = 1;
        $this->userIsAuthenticated();

        $dialog = $this->sampleCommand->getHelper('question');
        $dialog->setInputStream($this->getInputStream("foo"));

        $commandTester = new CommandTester($this->sampleCommand);
        $commandTester->execute([
            'command' => $this->sampleCommand->getName(),
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
}

class SampleCommand extends KbizeCommand
{
    public function __construct(KbizeKernelFactory $kernel, array $settings = [])
    {
        parent::__construct($kernel, $settings);
    }

    protected function options()
    {
        return [[
            'name'        => 'profile',
            'shortcut'    => 'e',
            'mode'        => InputOption::VALUE_REQUIRED,
            'description' => 'You can use different configuration for different profile',
            'default'     => 'default'
        ]];
    }

    protected function configure()
    {
        parent::configure();

        $this->setName('sample:command')
            ->addOption(new LazyInputOption(
                'option',
                'o',
                InputOption::VALUE_OPTIONAL | LazyInputOption::OPTION_IS_LAZY,
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
