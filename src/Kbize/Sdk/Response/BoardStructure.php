<?php
namespace Kbize\Sdk\Response;

class BoardStructure
{
    /**
     *
     array (
         'columns' => array (
             0 => array (
                 'position' => 0,
                 'lcname' => 'Backlog',
                 'section' => 'backlog',
                 'path' => 'backlog_5',
                 'description' => '',
                 'lcid' => '5',
             ),
         ),
         'lanes' => array (
             0 => array (
                 'position' => '0',
                 'lcname' => 'Subeng',
                 'path' => 'lane_4',
                 'description' => '',
                 'lcid' => '4',
                 'color' => '#FFFFFF',
             ),
        )
    )
     */
    public static function fromArrayResponse(array $response)
    {
        return new self($response);
    }

    private function __construct(array $data)
    {
        $this->data = $data;
    }

    public function columns()
    {
        return $this->data['columns'];
    }

    public function lanes()
    {
        return $this->data['lanes'];
    }
}
