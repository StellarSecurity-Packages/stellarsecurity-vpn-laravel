<?php

namespace StellarSecurity\LaravelVpn\Services;

use StellarSecurity\LaravelVpn\Http\VpnHttpClient;
use StellarSecurity\LaravelVpn\Contracts\VpnServerClientInterface;

class VpnServerClient implements VpnServerClientInterface
{
    protected string $baseUrl;
    protected VpnHttpClient $http;

    public function __construct(string $baseUrl, string $username, string $password, int $timeout)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->http    = new VpnHttpClient($username, $password, $timeout);
    }

    public function listServers(): array
    {
        $url = $this->baseUrl . '/v1/vpnservercontroller/list';
        return $this->http->get($url);
    }

    public function listServersRaw(): array
    {
        $url = $this->baseUrl . '/v1/vpnservercontroller/list';
        return $this->http->getRaw($url);
    }

    public function issueCredentials(int $userId, string $deviceId): array
    {
        $url = $this->baseUrl . '/v1/vpncredentialcontroller/credentials';
        return $this->http->post($url, [
            'user_id'   => $userId,
            'device_id' => $deviceId,
        ]);
    }

    public function resetDevice(int $userId, string $deviceId): bool
    {
        $url = $this->baseUrl . '/v1/vpncredentialcontroller/reset';
        $res = $this->http->post($url, [
            'user_id'   => $userId,
            'device_id' => $deviceId,
        ]);
        return ($res['response_code'] ?? 0) === 200;
    }

    public function resetAll(int $userId): bool
    {
        $url = $this->baseUrl . '/v1/vpncredentialcontroller/reset-all';
        $res = $this->http->post($url, [
            'user_id' => $userId,
        ]);
        return ($res['response_code'] ?? 0) === 200;
    }
}