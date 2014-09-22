<?php
namespace Kbize\Http;

use GuzzleHttp\Message\ResponseInterface;

class GuzzleResponse implements Response
{
    private function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public static function from(ResponseInterface $response)
    {
        return new self($response);
    }

    public function body()
    {
        return $this->response->getBody()->__toString();
    }

    public function json()
    {
        return $this->response->json();
    }
}
