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
use Kbize\Console\Output\TaskShowOutput;

class TaskListCommand extends KbizeCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('task:list')
            ->setAliases(['task-list', 'tasklist'])
            ->setDescription('List tasks of a specifc project and board')
            ->addOption(
                'short',
                '',
                InputOption::VALUE_NONE,
                'Display a minimal subset of information'
            )
            ->addOption(
                'show',
                's',
                InputOption::VALUE_NONE,
                'Display details for each tasks'
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
    }

    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $filters = $this->parseFilters($input);

        $taskCollection = $this->kernel
            ->getAllTasks($input->getOption('board'))
            ->filter($filters)
        ;

        if ($input->getOption('show')) {
            $taskShowOutput = new TaskShowOutput($output, $this->getHelper('alternate-table'));
            $taskShowOutput->render($taskCollection);
        } else {
            $taskListOutput = new TaskListOutput($output, $this->getHelper('table'));
            $taskListOutput->render($taskCollection);
        }
    }

    private function parseFilters(InputInterface $input)
    {
        $filters = $input->getArgument('filters');
        $nFilters = count($filters);
        if ($filters && 'show' == $filters[$nFilters - 1]) {
            array_pop($filters);
            $input->setOption('show', true);
        }

        return $filters;
    }
}
