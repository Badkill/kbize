<?php
namespace Kbize\Http;

use GuzzleHttp\ClientInterface;
use Kbize\Exception\ForbiddenException;
use Kbize\Http\Exception\ClientException;

class GuzzleClient implements Client
{
    public function __construct(ClientInterface $guzzle)
    {
        $this->guzzle = $guzzle;
    }

    public function post($url, array $data = [], array $headers = [])
    {
        try {
            return GuzzleResponse::from($this->guzzle->post($url, [
                'headers' => $headers,
                'json' => $data
            ]));
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if (403 === $e->getCode()) {
                throw new ForbiddenException(
                    /* $e->getMessage() . ' - ' . $e->getResponse()->getBody()->__toString(), */
                    $e->getMessage() . ' - ' . $e->getResponse()->getBody(),
                    $e->getCode(),
                    $e
                );
            }

            throw new ClientException(
                /* $e->getMessage() . ' - ' . $e->getResponse()->getBody()->__toString(), */
                $e->getMessage() . ' - ' . $e->getResponse()->getBody(),
                $e->getCode(),
                $e
            );
        }
    }
}
