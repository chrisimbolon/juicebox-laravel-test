<?php

namespace App\Console\Commands;

use App\Jobs\UpdateWeatherDataJob;
use Illuminate\Console\Command;

class TestWeatherUpdate extends Command
{
    protected $signature = 'weather:update';
    protected $description = 'Manually trigger weather data update';

    public function handle()
    {
        $this->info('🌤️  Dispatching weather update job...');
        
        UpdateWeatherDataJob::dispatch();
        
        $this->info('✅ Weather update job dispatched!');
        $this->info('📋 Check queue worker and logs to see the job processed.');
        
        return Command::SUCCESS;
    }
}