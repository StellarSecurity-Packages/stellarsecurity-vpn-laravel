<?php

namespace StellarSecurity\LaravelVpn;

use Illuminate\Support\ServiceProvider;
use StellarSecurity\LaravelVpn\Services\VpnServerClient;

class StellarVpnServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/stellar_vpn.php', 'stellar_vpn');

        $this->app->singleton(VpnServerClient::class, function () {
            return new VpnServerClient(
                config('stellar_vpn.base_url'),
                config('stellar_vpn.username'),
                config('stellar_vpn.password'),
                config('stellar_vpn.timeout'),
            );
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/stellar_vpn.php' => config_path('stellar_vpn.php'),
        ], 'stellar-vpn-config');
    }
}
