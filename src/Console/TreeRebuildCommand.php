<?php

declare(strict_types=1);

namespace TTBooking\Derevo\Console;

use Illuminate\Console\Command;
use TTBooking\Derevo\Contracts\TreeBuilder;

class TreeRebuildCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tree:rebuild
        {--c|compact : Prepare tree for archiving}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rebuild Derevo nested set';

    /**
     * Execute the console command.
     *
     * @param  TreeBuilder  $tree
     * @return void
     */
    public function handle(TreeBuilder $tree)
    {
        $tree->build();
    }
}
