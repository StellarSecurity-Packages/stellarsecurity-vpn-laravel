<?php

namespace StellarSecurity\LaravelVpn\Contracts;

interface VpnServerClientInterface
{
    public function listServers(): array;
    public function issueCredentials(int $userId, string $deviceId): array;
    public function resetDevice(int $userId, string $deviceId): bool;
    public function resetAll(int $userId): bool;
}
