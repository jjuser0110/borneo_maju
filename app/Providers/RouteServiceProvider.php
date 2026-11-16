<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use RecursiveIteratorIterator as Iterator;
use RecursiveDirectoryIterator as DirectoryIterator;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    protected $namespace = 'App\\Http\\Controllers';
    
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });

        $this->mapWebRoutes();
    }

    protected function mapWebRoutes()
    {  
        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace
        ], function ($router) {
            $iterator = new Iterator(new DirectoryIterator(base_path('routes')), Iterator::SELF_FIRST);
            // These are loaded elsewhere (or are not HTTP routes) — never pull them into the web group.
            $skip = ['web.php', 'api.php', 'channels.php', 'console.php'];
            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php' && !in_array($file->getFilename(), $skip, true)) {
                    require $file->getPathname();
                }
            }
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
