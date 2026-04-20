<?php

namespace App\Providers;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;

class RouteServiceProvider extends ServiceProvider
{
    protected $namespace = 'App\\Http\\Controllers';

    public function boot()
    {
        parent::boot();

        // Define the "api" rate limiter
        RateLimiter::for('api', function (Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));

            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));
        });
    }
}
