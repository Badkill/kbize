<?php
namespace Test\Unit\Kbize\Sdk\Response;

use Kbize\Sdk\Response\ProjectAndBoards;

class ProjectAndBoardsTest extends \PHPUnit_Framework_TestCase
{
    public function testProjectsReturnRightData()
    {
        $projectsAndBoardsResponse = ProjectAndBoards::fromArrayResponse(
            $this->sampleRawArrayData()
        );

        $this->assertEquals([[
            "name" => "ProjectName",
            "id"   => "1",
        ]], $projectsAndBoardsResponse->projects());
    }

    public function testBoardReturnRightData()
    {
        $projectsAndBoardsResponse = ProjectAndBoards::fromArrayResponse(
            $this->sampleRawArrayData()
        );

        $this->assertEquals([[
            "name" => "Service\/Merchant Integrations",
            "id" => "4",
        ], [
            "name" => "Tech Operations",
            "id" => "3",
        ], [
            "name" => "Main development",
            "id" => "2",
        ]], $projectsAndBoardsResponse->boards(1));
    }

    /**
     * @expectedException \Exception
     */
    public function testBoardsThrowAnExceptionInCaseOfNonexistentProjectId()
    {
        $projectsAndBoardsResponse = ProjectAndBoards::fromArrayResponse(
            $this->sampleRawArrayData()
        );

        $projectsAndBoardsResponse->boards(2);
    }

    private function sampleRawArrayData()
    {
        return [
            "projects" => [[
                "name" => "ProjectName",
                "id" => "1",
                "boards" => [[
                    "name" => "Service\/Merchant Integrations",
                    "id" => "4",
                ], [
                    "name" => "Tech Operations",
                    "id" => "3",
                ], [
                    "name" => "Main development",
                    "id" => "2",
                ]]
            ]]
        ];
    }
}
