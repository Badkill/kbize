<?php
namespace Test\Kbize\Console\Output;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Helper\TableHelper;

use Kbize\Collection\Tasks;
use Kbize\Console\Output\TaskListOutput;

class TaskListOutputTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->output = $this->getMock('Symfony\Component\Console\Output\OutputInterface');
        $this->table = $this->getMock('Symfony\Component\Console\Helper\TableHelper');

        $this->taskListOutput = new TaskListOutput($this->output, $this->table);
    }

    public function testRenderTableWithRightData()
    {
        $this->table->expects($this->once())
            ->method('setLayout')
            ->with(TableHelper::LAYOUT_BORDERLESS)
            ->will($this->returnValue($this->table))
        ;

        $this->table->expects($this->once())
            ->method('setCellRowContentFormat')
            ->with('%s ')
            ->will($this->returnValue($this->table))
        ;

        $this->table->expects($this->once())
            ->method('setHeaders')
            ->with([
                'ID',
                'Assignee',
                'Priority',
                'Lanename',
                'Columnname',
                'Title',
            ])
            ->will($this->returnValue($this->table))
        ;

        $this->table->expects($this->once())
            ->method('setRows')
            ->with([
                [
                    '10',
                    'name.surname',
                    'Average',
                    'Connectivity',
                    'Aborted',
                    'The Task Title',
                ],
                [
                    '<fg=red>15</fg=red>',
                    '<fg=red>paolo.rossi</fg=red>',
                    '<fg=red>Average</fg=red>',
                    '<fg=red>Connectivity</fg=red>',
                    '<fg=red>Customer Approval</fg=red>',
                    '<fg=red>Canada Fortumo integration</fg=red>',
                ],
                [
                    '17',
                    'None',
                    'Average',
                    'Connectivity',
                    'Ongoing',
                    'Telcel SMT connectivity',
                ],
            ])
            ->will($this->returnValue($this->table))
        ;

        $this->table->expects($this->once())
            ->method('render')
            ->with($this->output)
            ;

        $this->taskListOutput->render($this->simpleTaskCollection()->slice(0, 3));
    }

    private function simpleTaskCollection($filters = [])
    {
        return Tasks::fromArray([
            [
                'taskid' => '10',
                'position' => '0',
                'type' => 'Feature request',
                'assignee' => 'name.surname',
                'title' => 'The Task Title',
                'subtasks' => '0',
                'subtaskscomplete' => '0',
                'color' => '#34a97b',
                'priority' => 'Average',
                'columnid' => 'done_61',
                'laneid' => '10',
                'leadtime' => 32,
                'blocked' => '0',
                'subtaskdetails' => [],
                'columnname' => 'Aborted',
                'lanename' => 'Connectivity',
                'columnpath' => 'Aborted',
                'logedtime' => 0,
                'links' => [
                    'child' => 0,
                    'mirror' => 0,
                    'parent' => 0,
                ],
            ],
            [
                'taskid' => '15',
                'position' => '1',
                'type' => 'None',
                'assignee' => 'paolo.rossi',
                'title' => 'Canada Fortumo integration',
                'description' => 'Front end integration, to continue our business after our shortcode is suspended (15K/month)',
                'subtasks' => '0',
                'subtaskscomplete' => '0',
                'color' => '#34a97b',
                'priority' => 'Average',
                'columnid' => 'progress_15',
                'laneid' => '10',
                'leadtime' => 32,
                'blocked' => '1',
                'blockedreason' => 'Ultimate api for passive billing',
                'subtaskdetails' => [],
                'columnname' => 'Customer Approval',
                'lanename' => 'Connectivity',
                'columnpath' => 'Customer Approval',
                'logedtime' => 0,
                'links' => [
                    'child' => 0,
                    'mirror' => 0,
                    'parent' => 0,
                ],
            ],
            [
                'taskid' => '17',
                'position' => '0',
                'type' => 'Feature request',
                'assignee' => 'None',
                'title' => 'Telcel SMT connectivity',
                'description' => 'Porting of subscriptions and new activations for MX/Telcel on the new connectivity, where renewals are performed by the operator',
                'subtasks' => '0',
                'subtaskscomplete' => '0',
                'color' => '#34a97b',
                'priority' => 'Average',
                'columnid' => 'progress_2',
                'laneid' => '10',
                'leadtime' => 32,
                'blocked' => '0',
                'subtaskdetails' => [],
                'columnname' => 'Ongoing',
                'lanename' => 'Connectivity',
                'columnpath' => 'Ongoing',
                'logedtime' => 0,
                'links' => [
                    'child' => 0,
                    'mirror' => 0,
                    'parent' => 0,
                ],
            ],
        ]);
    }
}
