<?php
namespace Kbize\Sdk\Response;

class AllTasks
{
    public static function fromArrayResponse(array $response)
    {
        return new self($response);
    }

    private function __construct(array $data)
    {
        $this->data = $data;
    }

    public function toArray()
    {
        return $this->data;
    }
}
