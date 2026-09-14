<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

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
        // Rate limit login per email+IP (misal maksimal 5 percobaan per menit).
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)
                ->by(($request->input('email') ?: '').'|'.$request->ip())
                ->response(function () {
                    return redirect()->route('login')
                        ->withErrors(['email' => 'Terlalu banyak percobaan login. Silakan coba lagi dalam beberapa menit.'])
                        ->withInput();
                });
        });

        // Rate limit pembuatan order per meja+IP (misal maksimal 5 pesanan per menit)
        // untuk mencegah spam order + notifikasi.
        RateLimiter::for('order-create', function (Request $request) {
            $table = $request->route('table');
            // Pakai identifier stabil: id bila sudah ter-binding, atau token string bila belum.
            $tableKey = $table instanceof \App\Models\RestaurantTable
                ? (string) $table->id
                : (is_scalar($table) ? (string) $table : 'anon');

            return Limit::perMinute(5)
                ->by($tableKey.'|'.$request->ip())
                ->response(function () {
                    return back()->with('error', 'Terlalu banyak pesanan. Silakan tunggu sebentar lalu coba lagi.');
                });
        });
    }
}
