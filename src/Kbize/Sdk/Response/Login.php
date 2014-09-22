<?php
namespace Kbize\Sdk\Response;

class Login extends Response
{
    /**
     * {
     *   "email":"name.surname@email.com",
     *   "username":"name.surname",
     *   "realname":"Name Surname",
     *   "companyname":"Company",
     *   "timezone":"0:0",
     *   "apikey":"9PnEptErijehaz2hV9i5UpjsfN4ORuRcbbJQK7F3"
     * }
     */
    public static function fromArrayResponse(array $response)
    {
        return new self($response);
    }

    private function __construct(array $data)
    {
        $this->data = $data;
    }

    public function data()
    {
        return $this->data;
    }

    public function apikey()
    {
        return $this->data['apikey'];
    }
}
