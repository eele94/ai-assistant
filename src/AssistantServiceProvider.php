<?php

namespace Eele94\Assistant;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Eele94\Assistant\Commands\AssistantCommand;

class AssistantServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('ai-assistant')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_ai-assistant_table')
            ->hasCommand(AssistantCommand::class);
    }
}
