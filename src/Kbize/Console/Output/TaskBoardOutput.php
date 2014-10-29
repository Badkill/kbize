<?php
namespace Kbize\Console\Output;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\TableHelper;
use Kbize\Sdk\Response\BoardStructure;
use Kbize\Collection\TasksInterface;
use Kbize\Console\String;

class TaskBoardOutput
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

    public function render(BoardStructure $structure, TasksInterface $taskCollection)
    {
        $columns = array_map(function ($column) {
            return $column['lcname'];
        }, $structure->columns());

        $this->table
            ->setHeaders($columns)
            ->setRows($this->rows($taskCollection, $structure->columns(), $structure->lanes()))
        ;

        $this->table->render($this->output);
    }

/*     public function render(TasksInterface $taskCollection) */
/*     { */
/*         $fieldsToDisplay = $this->fieldsToDisplay(null); */

/*         $this->table */
/*             ->setLayout(TableHelper::LAYOUT_BORDERLESS) */
/*             ->setCellRowContentFormat('%s ') */
/*             ->setHeaders($this->headers($fieldsToDisplay)) */
/*             ->setRows($this->rows($taskCollection, $fieldsToDisplay)) */
/*         ; */

/*         $this->table->render($this->output); */
/*     } */

/*     private function headers(array $fieldsToDisplay) */
/*     { */
/*         $headers = []; */
/*         foreach ($fieldsToDisplay as $field) { */
/*             $headers[] = ucfirst($this->translateField($field)); */
/*         } */

/*         return $headers; */
/*     } */

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

/*     /** */
/*      * @return string */
/*      *1/ */
/*     private function translateField($field, array $fixes = []) */
/*     { */
/*         if (array_key_exists($field, $this->headersFieldsTranslation)) { */
/*             return $this->headersFieldsTranslation[$field]; */
/*         } */

/*         return $field; */
/*     } */

    /**
     * @param TasksInterface $taskCollection
     */
    private function rows(TasksInterface $taskCollection, array $columns, array $lanes)
    {
        $rows = [];

        foreach ($lanes as $lane) {
            $rows[] = ['__LANETITLE__', $lane['lcname']];

            foreach ($columns as $column) {

                $row = [];
                foreach ($taskCollection->filter([
                    'laneid=' . $lane['lcid'],
                    'columnid=' . $column['lcid'],
                ])->tasks() as $task) {
                    $color = $task['blocked'] ? self::BLOCKED_COLOR : '';
                    $row[] = String::box($task[$field])->color($color)->__toString();

                }

                $rows[] = $row;
            }
        }

        return $rows;
    }

    private function organizeTask($boardStructure, $taskCollection)
    {
        $organized = [];
        foreach ($boardStructure['lanes'] as $lane) {
            $organized[$lane['lcid']] = [];
            foreach ($boardStructure['columns'] as $column) {
                $organized[$lane['lcid']][$column['path']] = [];
            }
        }

        $tasks = $taskCollection->filter([]);
        foreach ($tasks as $task) {
            $organized[$task['laneid']][$task['columnid']][] = $task;
        }

        return $organized;
    }
}
