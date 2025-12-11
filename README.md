# StellarSecurity Laravel VPN SDK

This package provides a Laravel-friendly SDK for integrating with the Stellar VPN backend from any Stellar UI API service.

## Installation

Add the package to your project (after publishing it to your chosen package registry):

```bash
composer require stellarsecurity/stellarsecurity-laravel-vpn
```

If you are not using Laravel package auto-discovery, register the service provider manually in `config/app.php`:

```php
'providers' => [
    // ...
    StellarSecurity\LaravelVpn\StellarVpnServiceProvider::class,
];
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=stellar-vpn-config
```

This will create `config/stellar_vpn.php`.

Environment variables used:

```env
STELLAR_VPN_BASE_URL=https://stellarvpnapiprod.azurewebsites.net/api
STELLAR_VPN_USERNAME=StellarVpnApiProd
STELLAR_VPN_PASSWORD=your-strong-password-here
STELLAR_VPN_TIMEOUT=10
```

> Note: Set the username and password to the Basic Auth credentials used by the Stellar VPN API.

## Available Methods

All core operations are exposed through `StellarSecurity\LaravelVpn\Services\VpnServerClient`.

You can type-hint it in your controllers or services and let Laravel inject it:

```php
use StellarSecurity\LaravelVpn\Services\VpnServerClient;

class VpnController
{
    public function __construct(private VpnServerClient $client) {}

    public function servers()
    {
        $servers = $client->listServers();

        return response()->json($servers);
    }
}
```

### 1. List VPN Servers

```php
$servers = $client->listServers();
```

- Calls: `GET /v1/vpnservercontroller/list`
- Returns: `array` of server entries, e.g.:

```php
[
    [
        'id'            => 'eu-1',
        'name'          => 'Switzerland – Zurich',
        'country'       => 'CH',
        'lat'           => 47.3769,
        'lon'           => 8.5417,
        'protocols'     => ['udp', 'tcp'],
        'hostname'      => 'ch-zurich-1.stellarsecurity.com',
        'config_url'    => 'https://cdn.stellarsecurity.com/ovpn/eu-1.ovpn',
        'config_sha256' => '...',
    ],
    // ...
]
```

### 2. Issue or Fetch VPN Credentials

```php
$payload = $client->issueCredentials($userId, $deviceId);
```

- Calls: `POST /v1/vpncredentialcontroller/credentials`
- Body:

```php
[
    'user_id'   => 123,
    'device_id' => 'some-device-id',
]
```

- Returns a response array from the API, typically:

```php
[
    'response_code'    => 200,
    'response_message' => 'OK',
    'data'             => [
        'username' => 'stvpn_xxx',
        'password' => 'plain_random_password_or_null',
    ],
]
```

### 3. Revoke Credentials for a Single Device

```php
$success = $client->resetDevice($userId, $deviceId);
```

- Calls: `POST /v1/vpncredentialcontroller/reset`
- Returns: `true` if `response_code === 200`, otherwise `false`.

### 4. Revoke ALL Credentials for a User

```php
$success = $client->resetAll($userId);
```

- Calls: `POST /v1/vpncredentialcontroller/reset-all`
- Returns: `true` if `response_code === 200`, otherwise `false`.

## Example Controller

```php
use StellarSecurity\LaravelVpn\Services\VpnServerClient;

class StellarVpnController
{
    public function __construct(private VpnServerClient $vpnClient) {}

    public function serverList()
    {
        return response()->json($this->vpnClient->listServers());
    }

    public function credentials()
    {
        $userId   = auth()->id();
        $deviceId = request()->input('device_id');

        $response = $this->vpnClient->issueCredentials($userId, $deviceId);

        return response()->json($response);
    }
}
```

## Notes

- All calls use HTTP Basic Auth against the Stellar VPN API.
- Errors from the remote API should be handled at the UI API level (e.g. logging, retries, custom HTTP responses).
