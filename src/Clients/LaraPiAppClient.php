<?php

namespace Rgalstyan\Larapi\Clients;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class LaraPiAppClient extends LaraPiAppAbstract
{
    protected string $apiVersion;

    public function __construct()
    {
        parent::__construct();
        $this->apiVersion = config('larapi.api_version');
    }

    public function get(string $uri, array $params = []): object|null
    {
        try {
            $queryString = http_build_query($params);
            $uri = "$this->apiVersion/$uri?$queryString";

            $response = $this->client->request('GET', $uri);
            return json_decode($response->getBody()->getContents());
        } catch (GuzzleException $e) {
            Log::error("Pi API Error - $uri - : " . $e->getMessage());
        }
        return null;
    }

    public function post(string $uri, array $params = []): object|null
    {
        try {
            $uri = "$this->apiVersion/$uri";

            $response = $this->client->request('POST', $uri, [
                'json' => $params
            ]);
            return json_decode($response->getBody()->getContents());
        } catch (GuzzleException $e) {
            Log::error("Pi API Error - $uri - : " . $e->getMessage());
        }
        return null;
    }
}
