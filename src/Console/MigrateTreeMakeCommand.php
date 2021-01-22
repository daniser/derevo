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
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'make:tree-migration {name : The name of the migration}
        {--create= : The table to be created}
        {--table= : The table to migrate}
        {--path= : The location where the migration file should be created}
        {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}
        {--fullpath : Output the full path of the migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Derevo nested set migration file';

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
