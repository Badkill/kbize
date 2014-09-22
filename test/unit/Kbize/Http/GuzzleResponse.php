<?php
namespace Test\Unit\Kbize\Http;

use GuzzleHttp\Message\ResponseInterface;
use Kbize\Http\GuzzleResponse;

class GuzzleResponseTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->embeddedResponse = $this->getMock('GuzzleHttp\Message\ResponseInterface');
        $this->response = GuzzleResponse::from($this->embeddedResponse);
    }

    public function testBodyReturnsOriginalBodyString()
    {
        $this->embeddedResponse->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue(new StreamMessage('foo')))
        ;

        $this->assertEquals('foo', $this->response->body());
    }

    public function testJsonCallEmbeddedJson()
    {
        $json = ['foo' => 'bar'];

        $this->embeddedResponse->expects($this->once())
            ->method('json')
            ->will($this->returnValue($json))
        ;

        $this->assertEquals($json, $this->response->json());
    }
}

class StreamMessage
{
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function __toString()
    {
        return $this->data;
    }
}
