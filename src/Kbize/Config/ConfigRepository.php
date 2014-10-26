<?php
namespace Kbize\Config;

interface ConfigRepository {
    public function toArray();

    /**
     * @return void
     */
    public function destroy();

    /**
     * @return FilesystemConfigRepository
     */
    public function replace(array $data);

    /**
     * @return void
     */
    public function store();
}
