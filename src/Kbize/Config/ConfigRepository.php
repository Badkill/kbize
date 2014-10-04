<?php
namespace Kbize\Config;

interface ConfigRepository {
    public function toArray();

    public function destroy();

    public function replace(array $data);

    public function store();
}
