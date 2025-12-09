<?php

namespace Mhmadahmd\FilamentSaas;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Livewire\Features\SupportTesting\Testable;
use Mhmadahmd\FilamentSaas\Commands\FilamentSaasCommand;
use Mhmadahmd\FilamentSaas\Testing\TestsFilamentSaas;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentSaasServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-saas';

    public static string $viewNamespace = 'filament-saas';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('mhmadahmd/filament-saas');
            });

        $configFileName = $this->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void {}

    public function packageBooted(): void
    {
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/filament-saas/{$file->getFilename()}"),
                ], 'filament-saas-stubs');
            }
        }

        // Handle Seeders
        if (app()->runningInConsole()) {
            $seederPath = __DIR__ . '/../database/seeders';
            if (file_exists($seederPath)) {
                foreach (app(Filesystem::class)->files($seederPath) as $file) {
                    $this->publishes([
                        $file->getRealPath() => database_path("seeders/{$file->getFilename()}"),
                    ], 'filament-saas-seeders');
                }
            }
        }

        // Testing
        Testable::mixin(new TestsFilamentSaas);
    }

    protected function getAssetPackageName(): ?string
    {
        return 'mhmadahmd/filament-saas';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            // AlpineComponent::make('filament-saas', __DIR__ . '/../resources/dist/components/filament-saas.js'),
            Css::make('filament-saas-styles', __DIR__ . '/../resources/dist/filament-saas.css'),
            Js::make('filament-saas-scripts', __DIR__ . '/../resources/dist/filament-saas.js'),
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            FilamentSaasCommand::class,
        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            'create_plans_table',
            'create_plan_features_table',
            'create_plan_subscriptions_table',
            'create_plan_subscription_usage_table',
            'create_subscription_payments_table',
        ];
    }

    protected function shortName(): string
    {
        return Str::after(static::$name, 'filament-');
    }
}
