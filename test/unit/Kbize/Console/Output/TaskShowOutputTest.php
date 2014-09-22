<?php
namespace Test\Kbize\Console\Output;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Helper\TableHelper;

use Kbize\Collection\Tasks;
use Kbize\Console\Output\TaskShowOutput;

class TaskShowOutputTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->output = $this->getMock('Symfony\Component\Console\Output\OutputInterface');
        $this->table = $this->getMock('Symfony\Component\Console\Helper\TableHelper');

        $this->taskShowOutput = new TaskShowOutput($this->output, $this->table);
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
                'Name',
                'Value',
            ])
            ->will($this->returnValue($this->table))
        ;

        $this->table->expects($this->once())
            ->method('setRows')
            ->with([
                ['<options=bold>taskid</options=bold>', '<options=bold>15</options=bold>'],
                ['position', '1'],
                ['type', 'None'],
                ['<options=bold>assignee</options=bold>', '<options=bold>paolo.rossi</options=bold>'],
                ['<options=bold>title</options=bold>', '<options=bold>Canada Fortumo integration</options=bold>'],
                ['<options=bold>description</options=bold>', '<options=bold>Front end integration, to continue our business after our shortcode is suspended (15K/month)</options=bold>'],
                ['subtasks', '0'],
                ['subtaskscomplete', '0'],
                ['color', '#34a97b'],
                ['priority', 'Average'],
                ['columnid', 'progress_15'],
                ['laneid', '10'],
                ['leadtime', 32],
                ['<fg=red>blocked</fg=red>', '<fg=red>1</fg=red>'],
                ['<fg=red>blockedreason</fg=red>', '<fg=red>Ultimate api for passive billing</fg=red>'],
                ['columnname', 'Customer Approval'],
                ['lanename', 'Connectivity'],
                ['columnpath', 'Customer Approval'],
                ['logedtime', '0'],
            ])
            ->will($this->returnValue($this->table))
        ;

        $this->table->expects($this->once())
            ->method('render')
            ->with($this->output)
            ;

        $this->taskShowOutput->render($this->simpleTaskCollection()->slice(0, 3));
    }

    private function simpleTaskCollection($filters = [])
    {
        return Tasks::fromArray([
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
        ]);
    }
}
