<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Native\Desktop\Facades\Window;
use Native\Desktop\Contracts\ProvidesPhpIni;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        $baseDir = getenv('APPDATA') . DIRECTORY_SEPARATOR . 'bculms';
        $dbPath  = $baseDir . DIRECTORY_SEPARATOR . 'library.sqlite';
        $flag    = $baseDir . DIRECTORY_SEPARATOR . '.db_initialized';

        if (!is_dir($baseDir)) {
            mkdir($baseDir, 0755, true);
        }

        config([
            'database.connections.sqlite.database' => $dbPath,
        ]);

        putenv("DB_DATABASE=$dbPath");
        $_ENV['DB_DATABASE'] = $dbPath;

        if (!file_exists($flag)) {

            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true]);

            file_put_contents($flag, date('Y-m-d H:i:s'));
        }

        Window::open()
            ->maximized()
            ->hideMenu()
            ->showDevTools(false);
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [
            'memory_limit'       => '256M',
            'max_execution_time' => '60',
            'date.timezone'      => 'Asia/Manila',
            'display_errors'     => 'Off',
            'log_errors'         => 'On',
        ];
    }
}
