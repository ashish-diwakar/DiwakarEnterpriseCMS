<?php

// PHPUnit-only isolation: never load the application's real .env or cached configuration.
$testRoot = sys_get_temp_dir().DIRECTORY_SEPARATOR.'dss-cms-tests-'.bin2hex(random_bytes(12));
foreach (['', '/environment', '/storage/framework/views', '/storage/framework/cache', '/storage/framework/sessions', '/storage/logs', '/bootstrap'] as $directory) {
    if (! mkdir($testRoot.$directory, 0700, true) && ! is_dir($testRoot.$directory)) {
        throw new RuntimeException('Unable to create isolated test resources.');
    }
}
define('CMS_TEST_ROOT', $testRoot);
$environment = [
    'APP_ENV' => 'testing', 'APP_KEY' => 'base64:'.base64_encode(str_repeat('t', 32)),
    'APP_URL' => 'http://localhost', 'DB_CONNECTION' => 'sqlite', 'DB_DATABASE' => ':memory:', 'DB_URL' => '',
    'CACHE_STORE' => 'array', 'SESSION_DRIVER' => 'array', 'QUEUE_CONNECTION' => 'sync', 'MAIL_MAILER' => 'array',
    'APP_CONFIG_CACHE' => $testRoot.'/bootstrap/config.php', 'APP_ROUTES_CACHE' => $testRoot.'/bootstrap/routes.php',
    'APP_PACKAGES_CACHE' => $testRoot.'/bootstrap/packages.php', 'APP_SERVICES_CACHE' => $testRoot.'/bootstrap/services.php',
    'APP_EVENTS_CACHE' => $testRoot.'/bootstrap/events.php', 'VIEW_COMPILED_PATH' => $testRoot.'/storage/framework/views',
];
foreach ($environment as $name => $value) {
    putenv($name.'='.$value);
    $_ENV[$name] = $_SERVER[$name] = $value;
}
require dirname(__DIR__).'/vendor/autoload.php';
register_shutdown_function(static function () use ($testRoot): void {
    // Only the unique directory created above; never project storage or another run.
    if (is_link($testRoot) || realpath(dirname($testRoot)) !== realpath(sys_get_temp_dir()) || ! str_starts_with(basename($testRoot), 'dss-cms-tests-')) {
        return;
    }
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($testRoot, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($files as $file) {
        if ($file->isLink()) {
            unlink($file->getPathname());
        } elseif ($file->isDir()) {
            rmdir($file->getPathname());
        } else {
            unlink($file->getPathname());
        }
    }
    rmdir($testRoot);
});
