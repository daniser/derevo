<?php

declare(strict_types=1);

namespace TTBooking\Derevo;

use Illuminate\Support\ServiceProvider;

class DerevoServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/derevo.php' => $this->app->configPath('derevo.php'),
        ], 'config');

        $this->commands([
            Console\TreeMakeCommand::class,
            Console\MigrateTreeMakeCommand::class,
            Console\TreeRebuildCommand::class,
        ]);
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
}
