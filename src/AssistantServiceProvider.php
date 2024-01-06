<?php

namespace Eele94\Assistant;

use Eele94\Assistant\Commands\AssistantCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
