<?php

namespace Parents\RequestPause\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearPauseKeysCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:clear-pause-keys {key?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear keys for pausing jobs';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if($queue = $this->argument('key')){
            Cache::forget("pause_".$queue."_queue");
            return;
        }

        foreach (config('job_pause.queues') as $queue){
            Cache::forget("pause_".$queue."_queue");
        }
    }

}