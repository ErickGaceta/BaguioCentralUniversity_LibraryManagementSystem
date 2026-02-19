<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();

// Initialize database for PHP Desktop (only in web context)
if (php_sapi_name() !== 'cli') {
    $app->booted(function () {
        $storageDir = storage_path('app');
        $initFlagFile = $storageDir . DIRECTORY_SEPARATOR . '.db_initialized';

        try {
            $dbPath = config('database.connections.sqlite.database');
            if (is_callable($dbPath)) {
                $dbPath = $dbPath();
            }

            $dbExists = file_exists($dbPath);
            $initFlagExists = file_exists($initFlagFile);

            // Skip if already initialized
            if ($initFlagExists && $dbExists) {
                return;
            }

            // Check existing database
            if ($dbExists && !$initFlagExists) {
                try {
                    DB::connection()->getPdo();
                    if (Schema::hasTable('migrations')) {
                        file_put_contents($initFlagFile, date('Y-m-d H:i:s'));
                        return;
                    }
                } catch (\Exception $e) {
                    // Continue
                }
            }

            // Initialize
            Artisan::call('optimize', ['--force' => true]);
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true]);

            if (!file_exists($storageDir)) {
                mkdir($storageDir, 0755, true);
            }
            file_put_contents($initFlagFile, date('Y-m-d H:i:s'));
        } catch (\Exception $e) {
            error_log("Error initializing database: " . $e->getMessage());
        }
    });
}

return $app;
