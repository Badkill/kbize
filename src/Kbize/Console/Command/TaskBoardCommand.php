<?php
namespace Kbize\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Kbize\Console\Output\TaskBoardOutput;

class TaskBoardCommand extends KbizeCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('task:board')
            ->setAliases(['task-board', 'taskboard'])
            ->setDescription('Display tasks of a specifc project and board in a "board" format')
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
                "Filters to filter result, you can use any word an it will be searched on any field, \n" .
                "You can use @name to filter on asignee column only\n" .
                "You can use =id to filter on taskid column only"
            )
        ;
    }

    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $filters = $input->getArgument('filters');

        $boardStructure = $this->kernel
            ->getBoardStructure($input->getOption('board'))
        ;

        $taskCollection = $this->kernel
            ->getAllTasks($input->getOption('board'))
            ->filter($filters)
        ;

        $taskListOutput = new TaskBoardOutput($output, $this->getHelper('row-title-table'));
        $taskListOutput->render($boardStructure, $taskCollection);
    }
}
