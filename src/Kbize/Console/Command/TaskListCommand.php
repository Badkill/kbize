<?php
namespace Kbize\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Helper\TableHelper;

use Kbize\KbizeKernel;
use Kbize\Console\MissingMandatoryParametersRequest;
use Kbize\Console\String;
use Kbize\Console\Output\TaskListOutput;

class TaskListCommand extends KbizeCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('task:list')
            ->setDescription('Saluta qualcuno')
            ->addOption(
                'short',
                '',
                InputOption::VALUE_NONE,
                'Display a minimal subset of information'
            )
            ->addOption(
                'own',
                'o',
                InputOption::VALUE_NONE,
                'Display only my own tasks'
            )
            ->addOption(
                'no-cache',
                'x',
                InputOption::VALUE_NONE,
                'Do not use cached data'
            )
            ->addArgument(
                'filters',
                InputArgument::IS_ARRAY,
                []
            )
        ;

        $this->missingParameterRequest = new MissingMandatoryParametersRequest([
            'project' => function () {
                return $this->kernel->getProjects();
            },
            'board' => function (InputInterface $input) {
                return $this->kernel->getBoards($input->getOption('project'));
            }
        ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $taskCollection = $this->kernel->getAllTasks($input->getOption('board'));

        $taskListOutput = new TaskListOutput($output, $this->getHelper('table'));
        $taskListOutput->render($taskCollection->filter($input->getArgument('filters')));
    }
}
