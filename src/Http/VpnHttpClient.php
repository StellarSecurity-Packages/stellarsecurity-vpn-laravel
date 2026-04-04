<?php

namespace StellarSecurity\LaravelVpn\Http;

use GuzzleHttp\Client;

class VpnHttpClient
{
    protected Client $client;

    public function __construct(string $username, string $password, int $timeout = 10)
    {
        $this->client = new Client([
            'auth'    => [$username, $password],
            'timeout' => $timeout,
        ]);
    }

    public function get(string $url): array
    {
        $res = $this->client->get($url);
        return json_decode($res->getBody()->getContents(), true) ?? [];
    }

    public function getRaw(string $url): array
    {
        $res = $this->client->get($url);

        return [
            'body' => (string) $res->getBody(),
            'content_type' => $res->getHeaderLine('Content-Type') ?: 'application/json; charset=UTF-8',
            'signature' => $res->getHeaderLine('X-Stellar-Signature'),
            'key_id' => $res->getHeaderLine('X-Stellar-Key-Id'),
            'issued_at' => $res->getHeaderLine('X-Stellar-Issued-At'),
            'expires_at' => $res->getHeaderLine('X-Stellar-Expires-At'),
        ];
    }

    public function post(string $url, array $data): array
    {
        $res = $this->client->post($url, ['form_params' => $data]);
        return json_decode($res->getBody()->getContents(), true) ?? [];
    }
}