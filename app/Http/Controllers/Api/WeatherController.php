<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    protected $cacheKey = 'weather_perth_current';

    public function current(Request $request)
    {
        $cached = Cache::get($this->cacheKey);
        if ($cached) {
            return response()->json(['source' => 'cache', 'data' => $cached]);
        }

        try {
            $data = $this->fetchWeather();
            Cache::put($this->cacheKey, $data, now()->addMinutes(15));
            return response()->json(['source' => 'api', 'data' => $data]);
        } catch (\Exception $e) {
            // If failure and cache exists, return cached (graceful fallback)
            if ($cached) {
                return response()->json(['source' => 'cache_fallback', 'data' => $cached]);
            }
            return response()->json(['message' => 'Failed to retrieve weather', 'error' => $e->getMessage()], 502);
        }
    }

    protected function fetchWeather()
    {
        // WeatherAPI.com Current Weather
        $key = config('services.weather.key') ?? env('WEATHER_API_KEY');
        
        if (!$key) {
            throw new \Exception('Weather API key not configured.');
        }

        $response = Http::get('http://api.weatherapi.com/v1/current.json', [
            'key' => $key,
            'q' => 'Perth,Australia',
            'aqi' => 'no'  
        ]);

        if ($response->failed()) {
            throw new \Exception('External API failed: ' . $response->body());
        }

        return $response->json();
    }
}