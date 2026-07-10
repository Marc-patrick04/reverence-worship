<?php

$dirs = [
    '/app/bootstrap/cache',
    '/app/storage/app',
    '/app/storage/app/public',
    '/app/storage/framework/cache',
    '/app/storage/framework/cache/data',
    '/app/storage/framework/sessions',
    '/app/storage/framework/testing',
    '/app/storage/framework/views',
    '/app/storage/logs',
];

foreach ($dirs as $dir) {
    if (! is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
}

$router = '/app/wasmer-router.php';
file_put_contents($router, <<<'PHP'
<?php

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$file = realpath('/app/public' . $path);

if ($file !== false && str_starts_with($file, '/app/public') && is_file($file)) {
    return false;
}

require '/app/public/index.php';
PHP);

passthru('php -t /app/public -S localhost:8080 ' . escapeshellarg($router), $exitCode);
exit($exitCode);
