<?php

declare(strict_types=1);

namespace TTBooking\Derevo;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class DerevoServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public array $singletons = [
        Contracts\TreeBuilder::class => TreeBuilder::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/derevo.php' => $this->app->configPath('derevo.php'),
            ], 'config');

            $this->commands([
                Console\TreeMakeCommand::class,
                Console\MigrateTreeMakeCommand::class,
                Console\NodeFactoryMakeCommand::class,
                Console\TreeRebuildCommand::class,
            ]);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/derevo.php', 'derevo');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array_keys($this->singletons);
    }
}
