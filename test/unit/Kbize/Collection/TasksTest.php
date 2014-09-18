<?php
namespace test\Kbize\Collection;

use Kbize\Collection\Tasks;

class TasksTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->tasks = Tasks::fromArray($this->sampleData());
    }

    public function testFilterWithAndStrategy()
    {
        $sampleData = $this->sampleData();
        $expectedTasks = Tasks::fromArray([$sampleData[0]]);
        $this->assertEquals($expectedTasks, $this->tasks->filter(['Aborted']));

        $sampleData = $this->sampleData();
        $expectedTasks = Tasks::fromArray([]);
        $this->assertEquals($expectedTasks, $this->tasks->filter(['Aborted', 'Ongoing']));
    }

    public function testFilterWithOrStrategy()
    {
        $sampleData = $this->sampleData();
        $expectedTasks = Tasks::fromArray([$sampleData[0], $sampleData[2]]);
        $this->assertEquals($expectedTasks, $this->tasks->filter(['Aborted', 'Ongoing'], false));
    }

    public function testSlice()
    {
        $slicedTasks = $this->tasks->slice(0, 1);

        $expectedTasks = Tasks::fromArray(array_slice($this->sampleData(), 0, 1));
        $this->assertEquals($expectedTasks, $slicedTasks);
    }

    private function sampleData()
    {
        return [
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
        ];
    }
}
