<?php

namespace Rgalstyan\Larapi\Clients;

use GuzzleHttp\Client;

abstract class LaraPiAppAbstract
{
    protected string $baseUrl;
    protected string $apiKey;
    protected Client $client;

    public function __construct()
    {
        $this->baseUrl = config('larapi.api_url');
        $this->apiKey = config('larapi.api_key');

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => "Key " . $this->apiKey
            ]
        ]);
    }
}
