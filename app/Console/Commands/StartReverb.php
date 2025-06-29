<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class StartReverb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reverb:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the Laravel Reverb WebSocket server';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Reverb WebSocket server...');
        
        $result = Process::forever()->run('php artisan reverb:start --host=' . env('REVERB_HOST', '127.0.0.1') . ' --port=' . env('REVERB_PORT', 8080));
        
        if ($result->successful()) {
            $this->info('Reverb started successfully!');
        } else {
            $this->error('Failed to start Reverb:');
            $this->error($result->errorOutput());
        }
    }
}
