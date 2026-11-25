<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        if (! defined('CMS_TEST_ROOT')) {
            throw new \RuntimeException('Use the isolated PHPUnit bootstrap before running application tests.');
        }
        $app = require dirname(__DIR__).'/bootstrap/app.php';
        $app->useEnvironmentPath(CMS_TEST_ROOT.'/environment');
        $app->useStoragePath(CMS_TEST_ROOT.'/storage');
        $app->addAbsoluteCachePathPrefix(substr(CMS_TEST_ROOT, 0, 3));
        $this->traitsUsedByTest = array_flip(class_uses_recursive(static::class));
        $app->make(Kernel::class)->bootstrap();
        $sqlite = $app['config']->get('database.connections.sqlite');
        if (! $app->environment('testing') || $app['config']->get('database.default') !== 'sqlite' || $sqlite['database'] !== ':memory:' || ! empty($sqlite['url'])) {
            throw new \RuntimeException('Unsafe test database configuration: only SQLite :memory: is allowed.');
        }
        $app['config']->set('database.connections', ['sqlite' => $sqlite]);
        $database = $app['db']->connection();
        foreach ($database->select('PRAGMA database_list') as $attached) {
            if ($attached->file !== '') {
                throw new \RuntimeException('A persistent database was selected for tests.');
            }
        }

        return $app;
    }
}
