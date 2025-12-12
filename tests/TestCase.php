<?php

namespace ShaydR\FilamentSmartExport\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use ShaydR\FilamentSmartExport\FilamentSmartExportServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // Additional setup
    }

    protected function getPackageProviders($app)
    {
        return [
            FilamentSmartExportServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/database/migrations/create_test_table.php';
        $migration->up();
        */
    }
}
