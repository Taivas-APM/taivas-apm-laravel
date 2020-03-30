<?php

namespace TaivasAPM\Console;

use Illuminate\Console\Command;
use TaivasAPM\Tracking\Persister;

class PersistCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'taivas:persist';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Persist all queued requests to the database';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        /** @var Persister $persister */
        $persister = app('taivas.persister');
        $persister->persistQueuedJobs();
    }
}
