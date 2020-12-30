<?php

declare(strict_types=1);

namespace TTBooking\Derevo\Console;

use Illuminate\Console\Command;

class MakeTreeCommand extends Command
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
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->warn('Not implemented.');
    }
}
