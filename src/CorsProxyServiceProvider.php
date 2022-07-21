<?php

namespace ttungbmt\CorsProxy;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use ttungbmt\CorsProxy\Commands\CorsProxyCommand;

class CorsProxyServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-cors-proxy')
            ->hasRoute('web')
        ;
    }
}
