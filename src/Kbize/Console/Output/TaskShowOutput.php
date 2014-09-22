<?php
namespace Kbize\Console\Output;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Helper\TableHelper;

use Kbize\Collection\TasksInterface;
use Kbize\Console\String;

class TaskShowOutput
{
    const BLOCKED_COLOR = 'red';

    private $output;
    private $table;

    private $headersFieldsTranslation = [
        'taskid' => 'ID',
    ];

    public function __construct(OutputInterface $output, TableHelper $table)
    {
        $this->output = $output;
        $this->table = $table;
    }

    public function render(TasksInterface $taskCollection)
    {
        $this->table
            ->setLayout(TableHelper::LAYOUT_BORDERLESS)
            ->setCellRowContentFormat('%s ')
            ->setHeaders(['Name', 'Value']);
        ;

        foreach ($taskCollection->tasks() as $task) {
            $this->showTask($task);
            $this->output->writeln('');
            $this->output->writeln('################################################################'); //FIXME:!
            $this->output->writeln('');
        }
    }

    private function showTask($task)
    {
        $rows = [];
        foreach ($task as $field => $value) {
            if (is_array($value)) {
                //TODO:!!
                continue;
            }

            $rows[] = [
                /* String::box($field)->color($color)->__toString(), */
                $this->decorateString($field, $field)->__toString(),
                $this->decorateString($field, $value)->__toString(),
            ];
        }

        $this->table
            ->setRows($rows)
            ->render($this->output)
        ;
    }

    private function decorateString($field, $value)
    {
        $color = strstr($field, 'blocked') !== false && $value ? self::BLOCKED_COLOR : '';
        $value = String::box($value)->color($color);
        if (in_array($field, ['taskid', 'assignee', 'title', 'description'])) {
            $value = $value->bold();
        }

        return $value;
    }
}
