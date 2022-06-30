<?php

namespace Parents\RequestPause\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ClearPauseKeysCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:clear-pause-keys';

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
     * @return bool
     */
    public function handle(): bool
    {
        dd(DB::table('jobs')->pluck('queue'));
        foreach (array_unique() as $queue){
            Cache::forget('pause_'.$queue.'_queue');
        }
    }
}
