<?php
namespace Kbize\Http;

interface Client
{
    public function post($url, array $data);
}
