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

if (php_sapi_name() !== 'cli') {
    $app->booted(function () {

        $dbPath = database_path('database.sqlite');
        config(['database.connections.sqlite.database' => $dbPath]);

        $storageDir   = storage_path('app');
        $initFlagFile = $storageDir . DIRECTORY_SEPARATOR . '.db_initialized';

        $dirs = [
            $storageDir,
            storage_path('framework/sessions'),
            storage_path('framework/cache/data'),
            storage_path('framework/views'),
            storage_path('logs'),
            base_path('bootstrap/cache'),
            dirname($dbPath),
        ];

        foreach ($dirs as $dir) {
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
        }

        try {
            $dbExists       = file_exists($dbPath);
            $initFlagExists = file_exists($initFlagFile);

            if ($initFlagExists && $dbExists) {
                return;
            }

            if ($dbExists && !$initFlagExists) {
                try {
                    DB::connection()->getPdo();
                    if (Schema::hasTable('migrations')) {
                        file_put_contents($initFlagFile, date('Y-m-d H:i:s'));
                        return;
                    }
                } catch (\Exception $e) {
                    error_log('[BCU Library] DB check failed: ' . $e->getMessage());
                }
            }

            if (!$dbExists) {
                file_put_contents($dbPath, '');
            }

            // Swallow Artisan console output — any leaked output before
            // headers are sent will cause PHPDesktop's 500 CGI header error.
            ob_start();
            Artisan::call('migrate', ['--force' => true]);
            ob_end_clean();

            ob_start();
            Artisan::call('db:seed', ['--force' => true]);
            ob_end_clean();

            file_put_contents($initFlagFile, date('Y-m-d H:i:s'));
        } catch (\Exception $e) {
            // Also clean any partial buffer on failure
            if (ob_get_level() > 0) {
                ob_end_clean();
            }
            error_log('[BCU Library] DB init failed: ' . $e->getMessage());
        }
    });
}

return $app;
