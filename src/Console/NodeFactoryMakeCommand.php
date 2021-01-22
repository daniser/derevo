<?php

declare(strict_types=1);

namespace TTBooking\Derevo\Console;

use Illuminate\Database\Console\Factories\FactoryMakeCommand;

class NodeFactoryMakeCommand extends FactoryMakeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:node-factory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Derevo node factory';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Node factory';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/factory.stub';
    }
}
