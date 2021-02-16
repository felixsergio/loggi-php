<?php

namespace FelixLoggi;

use FelixLoggi\GraphQL;
use FelixLoggi\Logger;

class Request extends Http
{

    const SANDBOX = 'https://staging.loggi.com';
    const PRODUCTION = 'https://www.loggi.com';

    /**
     * @var array
     */
    private $code = null;
    private $url = null;
    private $base_uri = null;
    private $headers = null;
    private $body = null;

    /**
     * Query constructor.
     * @param array $value
     */
    function __construct(string $method, string $url, $graph)
    {
        $query = (string) new GraphQL($graph);

        $this->query = $query;

        $logger = new Logger();
        $this->logger = $logger->execute();

        $this->request($method, $url, [
            'body' => $query,
            'headers' => [
                'Content-Type: application/json',
                'cache-control: no-cache',
                'Authorization: ApiKey '. getenv('LOGGI_API_EMAIL') .':' .  getenv('LOGGI_API_KEY')
            ]
        ]);
    }

    protected function request(string $method, string $url, array $options = [])
    {

        $url = '/graphql' . $url;

        $this->base_uri = getenv('LOGGI_ENV') == 'sandbox'? static::SANDBOX : static::PRODUCTION;
        $this->url = $url;

        $curl = curl_init($url);

        if($method == 'post') {
            curl_setopt($curl, CURLOPT_POST, true);
        }
        curl_setopt($curl, CURLOPT_URL, $this->base_uri . $url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Loggi PHP SDK');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $options['headers']);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $options['body']);

        if ($this->logger !== null) {
            $this->logger->debug('Requisição', [
                    sprintf('%s %s', $method, $url),
                    $options['headers'],
                    $options['body']
                ]
            );
        }

        $response   = curl_exec($curl);

        if($response === false){
            $this->errors = curl_error($curl);

            if (curl_errno($curl)) {
                $message = sprintf('cURL error[%s]: %s', curl_errno($curl), curl_error($curl));

                $this->logger->error($message);
            }

        }else{
            $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            $body = substr($response, $header_size);

            $this->code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $this->headers =  curl_getinfo($curl, CURLINFO_HEADER_OUT);
            $this->body = $this->isJson($body)? json_decode($body) : $body;

            if ($this->logger !== null) {
                $this->logger->debug('Resposta', [
                    sprintf('Código de status: %s', $this->code),
                    $this->body
                ]);
            }
        }

        return $this;
    }

    public function getQuery()
    {
        return $this->query ?? null;
    }

    public function getBody()
    {
        return $this->body ?? null;
    }

    public function getData()
    {
        return $this->body->data ?? null;
    }

    public function hasErrors()
    {
        return empty($this->errors) && empty($this->body->errors) ? false : true;
    }

    public function getErrors()
    {
        $errors = $this->errors ?? null;
        $errors = $this->body->errors ?? $errors;

        return $errors;
    }

    public function isJson(string $string)
    {
        json_decode($string);
        return (json_last_error() === JSON_ERROR_NONE);
    }
}
