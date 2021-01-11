<?php

declare(strict_types=1);

namespace TTBooking\Derevo\Console;

use Illuminate\Database\Console\Migrations\MigrateMakeCommand;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;

class MigrateTreeMakeCommand extends MigrateMakeCommand
{
    /**
     * Create a new tree migration install command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  \Illuminate\Support\Composer  $composer
     * @return void
     */
    public function __construct(Filesystem $files, Composer $composer)
    {
        parent::__construct(new MigrationCreator($files, __DIR__.'/stubs'), $composer);
    }
}
