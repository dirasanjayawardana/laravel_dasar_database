<?php

namespace App\Providers;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        // DB::listen() --> melakukan debugging SQL Query yang dibuat oleh laravel, akan dipanggil setiap kali ada operasi yang dilakukan oleh laravel dtabase
        DB::listen(function (QueryExecuted $query) {
            Log::info($query->sql); // akan tersimpan di storage/logs/laravel.log
        });
    }
}
