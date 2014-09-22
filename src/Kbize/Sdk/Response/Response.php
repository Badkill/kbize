<?php
namespace Kbize\Sdk\Response;

abstract class Response implements \ArrayAccess
{
    protected $data;

    public function offsetExists($key)
    {
        return array_key_exists($key, $this->data);
    }

    public function offsetGet($key)
    {
        return $this->data[$key];
    }

    public function offsetSet($key, $value)
    {
        throw new \Exception('Immutable');
    }

    public function offsetUnset($key)
    {
        throw new \Exception('Immutable');
    }
}
