<?php

namespace Shayd\FilamentSmartExport;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentSmartExportServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-smart-export')
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews();
    }

    public function packageBooted(): void
    {
        // Publicar assets
        if ($this->app->runningInConsole()) {
            $this->publishes([
                $this->package->basePath('/../resources/css') => public_path('vendor/filament-smart-export/css'),
            ], "{$this->package->shortName()}-css");

            $this->publishes([
                $this->package->basePath('/../resources/js') => public_path('vendor/filament-smart-export/js'),
            ], "{$this->package->shortName()}-js");
        }
    }
}
