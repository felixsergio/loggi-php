<?php

namespace FelixLoggi;

use GuzzleHttp\Client;

class Http
{
    const GET = 'GET';
    const POST = 'POST';
    const PATCH = 'PATCH';
    const PUT = 'PUT';
    const DELETE = 'DELETE';

    public function get(string $url, array $options = [])
    {
        return $this->request(static::GET, $url, $options);
    }

    public function post(string $url, array $options = [])
    {
        return $this->request(static::POST, $url, $options);
    }

    public function patch(string $url, array $options = [])
    {
        return $this->request(static::PATCH, $url, $options);
    }

    public function put(string $url, array $options = [])
    {
        return $this->request(static::PUT, $url, $options);
    }

    public function delete(string $url, array $options = [])
    {
        return $this->request(static::DELETE, $url, $options);
    }

    protected function request(string $method, string $url, array $options = [])
    {
        $client = new Client();
        $request = $client->request(
            $method,
            $url,
            $options
        );

        return $request;
    }
}
