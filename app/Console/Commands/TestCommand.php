<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $signature = 'test {--filter= : Filter tests by name}';
    protected $description = 'Run the application tests';

    public function handle(): int
    {
        $filter = $this->option('filter');
        $command = 'vendor\\bin\\phpunit tests/Unit/';
        
        if ($filter) {
            $command .= " --filter=\"{$filter}\"";
        }

        $this->info('Running tests...');
        $exitCode = passthru($command);
        
        return $exitCode ?? 0;
    }
}
