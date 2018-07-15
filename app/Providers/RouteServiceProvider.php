<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot() {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map() {
		$this->mapPassportRoutes();

        $this->mapApiRoutes();

        // A définir en dernier
        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapPassportRoutes() {
		Passport::routes();
		Passport::tokensCan(\Scopes::all());

        Passport::enableImplicitGrant();

		Route::prefix('oauth')
			->group(base_path('routes/oauth.php'));
    }


    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes() {
        $services = config('auth.services');
        $route = Route::middleware('web')
            ->namespace($this->namespace);

		foreach ($services as $provider => $data) {
            $file = base_path('routes/auth/'.$provider.'.php');
            if (file_exists($file))
                $route->prefix('login/'.$provider)->group($file);
        }

        // A définir en dernier car la route '/' override tout
        $route->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes() {
        Route::prefix('api')
            ->namespace($this->namespace)
			->middleware('forceJson')
            ->group(base_path('routes/api.php'));
    }
}
