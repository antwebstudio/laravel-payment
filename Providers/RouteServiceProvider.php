<?php
namespace Ant\Payment\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'Ant\Payment\Http\Controllers';

    /**
     * Bootstrap any addon services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for the addon.
     *
     * @return void
     */
    public function map()
    {
        $this->mapWebRoutes();
        // $this->mapApiRoutes();
        // $this->mapDataTableRoutes();
    }

    /**
     * Define the "web" routes for the addon.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::group([
            'middleware' => 'web',
            'namespace'  => $this->namespace . '\Web',
        ], function ($router) {
            require $this->getPath('web.php');
        });
    }

    /**
     * Define the "api" routes for the addon.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::group([
            'middleware' => 'api',
            'namespace'  => $this->namespace . '\API',
            'prefix'     => 'api',
        ], function ($router) {
            require $this->getPath('api.php');
        });
    }

    protected function mapDataTableRoutes() {
        Route::group($this->routeDatatableConfiguration(), function () {
            require $this->getPath('datatable.php');
        });
    }

    /**
     * Get the datatable route group configuration array.
     *
     * @return array
     */
    private function routeDatatableConfiguration()
    {
        return [
            'middleware' => 'api',
            'namespace'  =>  $this->namespace . '\DataTable',
            'prefix'     => 'datatable',
        ];
    }

    protected function getPath($path)
    {
        return dirname(__DIR__).'/routes/'.$path;
    }
}
