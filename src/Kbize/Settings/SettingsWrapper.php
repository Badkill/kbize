<?php
namespace Kbize\Settings;

use Kbize\Config\FilesystemConfigRepository;

class SettingsWrapper implements \ArrayAccess
{
    private $data;
    private $configRepository;

    public function __construct(FilesystemConfigRepository $configRepository)
    {
        $this->configRepository = $configRepository;
        $this->data = $this->configRepository->toArray();
    }

    public function add(array $data)
    {
        $this->data = array_merge($this->data, $data);
        $this->configRepository->replace($this->data);
    }

    public function store()
    {
        $this->configRepository->store();
    }

    public function isValid()
    {
        return !empty($this->data);
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->data);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;

        return $this;
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);

        return $this;
    }
}
