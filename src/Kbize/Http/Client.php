<?php
namespace Kbize\Http;

interface Client
{
    /**
     * @return GuzzleResponse
     */
    public function post($url, array $data);
}
