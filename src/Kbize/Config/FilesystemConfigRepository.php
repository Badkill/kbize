<?php
namespace Kbize\Config;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Dumper;

class FilesystemConfigRepository implements ConfigRepository
{
    private $data = [];
    private $filePath;
    private $parser;
    private $dumper;

    public function __construct($filePath, Parser $parser, Dumper $dumper)
    {
        $this->filePath = $filePath;
        $this->parser = $parser;
        $this->dumper = $dumper;

        if (is_file($this->filePath)) {
            //TODO:! extract class to check file_put_contents
            $this->data = $this->parser->parse(file_get_contents($filePath));
            if (!$this->data) {
                $this->data = [];
            }
        }
    }

    public function toArray()
    {
        return $this->data;
    }

    public function destroy()
    {
        $this->data = [];
        $this->store();
    }

    public function replace(array $data)
    {
        $this->data = $data;

        return $this;
    }

    public function store()
    {
        //TODO:! extract class to check file_put_contents
        file_put_contents($this->filePath, $this->dumper->dump($this->data, 4));
    }
}
