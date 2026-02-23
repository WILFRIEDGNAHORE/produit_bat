<?php

namespace App\Providers;

use App\Models\PaymentSettings;
use App\Models\Settings;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */

public function boot(): void
{
    Paginator::useBootstrapFive();

    // Valeurs par défaut (important)
    $settings = null;
    $paymentSettings = [];

    // Charger Settings uniquement si la table existe
    if (Schema::hasTable('settings')) {
        $settings = Settings::first();

        if ($settings?->time_zone) {
            Config::set('app.timezone', $settings->time_zone);
        }
    }

    // Charger PaymentSettings uniquement si la table existe
    if (Schema::hasTable('payment_settings')) {
        $paymentSettings = PaymentSettings::query()
            ->pluck('value', 'key')
            ->toArray();

        // PayPal
        Config::set('paypal.mode', $paymentSettings['paypal_mode'] ?? 'sandbox');
        Config::set('paypal.currency', $paymentSettings['paypal_currency'] ?? 'USD');
        Config::set('paypal.validate_ssl', (bool)($paymentSettings['paypal_validate_ssl'] ?? false));

        Config::set('paypal.sandbox.client_id', $paymentSettings['paypal_client_id'] ?? '');
        Config::set('paypal.sandbox.client_secret', $paymentSettings['paypal_client_secret'] ?? '');
        Config::set('paypal.sandbox.app_id', $paymentSettings['paypal_app_id'] ?? 'APP-80W284485P519543T');

        Config::set('paypal.live.client_id', $paymentSettings['paypal_client_id'] ?? '');
        Config::set('paypal.live.client_secret', $paymentSettings['paypal_client_secret'] ?? '');
        Config::set('paypal.live.app_id', $paymentSettings['paypal_app_id'] ?? '');

        // SSLCommerz
        $testMode = ($paymentSettings['ssl_mode'] ?? 'sandbox') === 'sandbox';

        Config::set('sslcommerz.testmode', $testMode);
        Config::set(
            'sslcommerz.apiDomain',
            $testMode ? 'https://sandbox.sslcommerz.com' : 'https://securepay.sslcommerz.com'
        );

        Config::set('sslcommerz.apiCredentials.store_id', $paymentSettings['ssl_store_id'] ?? env('SSLCZ_STORE_ID'));
        Config::set('sslcommerz.apiCredentials.store_password', $paymentSettings['store_pass'] ?? env('SSLCZ_STORE_PASSWORD'));
    }

    // Partage global vers les vues (safe)
    View::composer('*', function ($view) use ($settings, $paymentSettings) {
        $view->with([
            'settings' => $settings,
            'paymentSettings' => $paymentSettings,
        ]);
    });
}
}
