<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share settings with all views
        // Using view composer to avoid issues during migration/setup if table doesn't exist yet
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            try {
                $settings = \Illuminate\Support\Facades\Cache::rememberForever('global_settings', function() {
                    return \App\Models\Setting::all()->pluck('value', 'key');
                });
                $view->with('globalSettings', $settings);
            } catch (\Exception $e) {
                // Determine if we are running in console to avoid errors during migration
                $view->with('globalSettings', collect([]));
            }
        });

        // Add Blade Directive for currency formatting
        \Illuminate\Support\Facades\Blade::directive('currency', function ($expression) {
            return "<?php echo ((\$globalSettings['currency_symbol'] ?? '$') . ' ' . number_format($expression, 2)); ?>";
        });
    }
}
