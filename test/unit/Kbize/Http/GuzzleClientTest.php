<?php
namespace Test\Unit\Kbize\Http;

use Kbize\Http\GuzzleClient;
use Kbize\Http\GuzzleResponse;
use GuzzleHttp\Exception\ClientException;

class GuzzleClientTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->guzzle = $this->getMock('GuzzleHttp\ClientInterface');
        $this->client = new GuzzleClient($this->guzzle);
    }

    public function testPostCallEmbeddedGuzzleClientAndWrapTheGuzzleResponse()
    {
        $rawResponse = $this->getMock('GuzzleHttp\Message\ResponseInterface');

        $url = 'http://fake.url';
        $headers = ['fake_header' => 1];
        $data    = ['first' => 1, 'second' => 2];

        $this->guzzle->expects($this->once())
            ->method('post')
            ->with($url, [
                'headers' => $headers,
                'json' => $data
            ])
            ->will($this->returnValue($rawResponse))
        ;

        $this->assertEquals(
            GuzzleResponse::from($rawResponse),
            $this->client->post($url, $data, $headers)
        );
    }

    /**
     * @expectedException Kbize\Http\Exception\ClientException
     */
    public function testGuzzleExceptionsAreWrappedIntoOwnExceptions()
    {
        $request = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $response = $this->getMock('GuzzleHttp\Message\ResponseInterface');

        $response->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue(404))
        ;

        $rawException = new ClientException('Not found', $request, $response);

        $this->guzzle->expects($this->once())
            ->method('post')
            ->will($this->throwException($rawException))
        ;

        $this->client->post('http://sample.com');
    }

    /**
     * @expectedException Kbize\Exception\ForbiddenException
     */
    public function testGuzzle403ExceptionAreWrappedIntoAForbiddenException()
    {
        $request = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $response = $this->getMock('GuzzleHttp\Message\ResponseInterface');

        $response->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue(403))
        ;

        $rawException = new ClientException('Forbidden', $request, $response);

        $this->guzzle->expects($this->once())
            ->method('post')
            ->will($this->throwException($rawException))
        ;

        $this->client->post('http://sample.com');
    }
}
