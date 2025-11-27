<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateWeatherDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('🌤️  Starting weather data update...');

            $key = config('services.weather.key') ?? env('WEATHER_API_KEY');
            
            if (!$key) {
                Log::error('❌ Weather API key not configured!');
                return;
            }

            // Fetch weather from WeatherAPI.com
            $response = Http::get('http://api.weatherapi.com/v1/current.json', [
                'key' => $key,
                'q' => 'Perth,Australia',
                'aqi' => 'no'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Update cache for 15 minutes
                Cache::put('weather_perth_current', $data, now()->addMinutes(15));
                
                $temp = $data['current']['temp_c'] ?? 'N/A';
                $condition = $data['current']['condition']['text'] ?? 'N/A';
                
                Log::info("✅ Weather data updated successfully! Perth: {$temp}°C, {$condition}");
            } else {
                Log::error('❌ Weather API request failed: ' . $response->body());
            }

        } catch (\Exception $e) {
            Log::error('❌ Failed to update weather data: ' . $e->getMessage());
        }
    }
}