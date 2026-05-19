<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting;
use Illuminate\Support\Facades\Config;

class SetSystemTimezone
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Retrieve timezone from settings
            // We use a simple query. In production, this should be cached.
            $timezone = Setting::where('key', 'timezone')->value('value');

            if ($timezone) {
                // Set Laravel Config
                Config::set('app.timezone', $timezone);

                // Set PHP Default Timezone
                date_default_timezone_set($timezone);
            }
        } catch (\Exception $e) {
            // If DB connection fails or table doesn't exist (e.g. during migration), 
            // we proceed with default config to avoid crashing the app entirely.
        }

        return $next($request);
    }
}
