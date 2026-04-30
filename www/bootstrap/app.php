<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$dbPath = getenv('APPDATA') . DIRECTORY_SEPARATOR . 'bculms' . DIRECTORY_SEPARATOR . 'library.sqlite';

$baseDir = dirname($dbPath);

if (!is_dir($baseDir)) {
    mkdir($baseDir, 0755, true);
}

if (!file_exists($dbPath)) {
    file_put_contents($dbPath, '');
}

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

return $app;
