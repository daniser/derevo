<?php

declare(strict_types=1);

namespace TTBooking\Derevo\Console;

use Illuminate\Foundation\Console\ModelMakeCommand;
use Illuminate\Support\Str;

class TreeMakeCommand extends ModelMakeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:tree';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Derevo nested set';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Tree';

    /**
     * Create a model factory for the model.
     *
     * @return void
     */
    protected function createFactory()
    {
        $factory = Str::studly($this->argument('name'));

        $this->call('make:node-factory', [
            'name' => "{$factory}Factory",
            '--model' => $this->qualifyClass($this->getNameInput()),
        ]);
    }

    /**
     * Create a migration file for the tree model.
     *
     * @return void
     */
    protected function createMigration()
    {
        $table = Str::snake(Str::pluralStudly(class_basename($this->argument('name'))));

        $this->call('make:tree-migration', [
            'name' => "create_{$table}_table",
            '--create' => $table,
        ]);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/node.stub';
    }
}
