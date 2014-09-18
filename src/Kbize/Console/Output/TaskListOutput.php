<?php
namespace Kbize\Console\Output;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Helper\TableHelper;

use Kbize\Collection\TasksInterface;
use Kbize\Console\String;

class TaskListOutput
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
        $fieldsToDisplay = $this->fieldsToDisplay(null);

        $this->table
            ->setLayout(TableHelper::LAYOUT_BORDERLESS)
            ->setCellRowContentFormat('%s ')
            ->setHeaders($this->headers($fieldsToDisplay))
            ->setRows($this->rows($taskCollection, $fieldsToDisplay))
        ;

        $this->table->render($this->output);
    }

    private function headers(array $fieldsToDisplay)
    {
        $headers = [];
        foreach ($fieldsToDisplay as $field) {
            $headers[] = ucfirst($this->translateField($field));
        }

        return $headers;
    }

    private function fieldsToDisplay($input)
    {
        return [
            'taskid',
            'assignee',
            'priority',
            'lanename',
            'columnname',
            'title',
        ];
        //FIXME:!
        $short = $input->getOption('short', false);

        return $this->settings[$short ? 'tasks.shortlist' : 'tasks.longlist'];
    }

    private function translateField($field, array $fixes = [])
    {
        if (array_key_exists($field, $this->headersFieldsTranslation)) {
            return $this->headersFieldsTranslation[$field];
        }

        return $field;
    }

    private function rows($taskCollection, $fieldsToDisplay)
    {
        $rows = [];

        foreach ($taskCollection->tasks() as $task) {
            $color = $task['blocked'] ? self::BLOCKED_COLOR : '';
            $row = [];
            foreach ($fieldsToDisplay as $field) {
                $row[] = String::box($task[$field])->color($color)->__toString();
            }

            $rows[] = $row;
        }

        return $rows;
    }
}
