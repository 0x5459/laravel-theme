<?php


namespace Ty666\LaravelTheme;


use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class LaravelThemeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $this->publishes([
            realpath(__DIR__ . '/../config/laravel-theme.php') => config_path('laravel-theme.php'),
        ]);
        if (!app()->runningInConsole()) {
            if (!$this->app->routesAreCached()) {
                $config = config('laravel-theme.screenshot_route');
                $router->group(array_merge(['namespace' => __NAMESPACE__], isset($config['options'])?$config['options']:[]), function ($router) use ($config){
                    $router->get((isset($config['url'])?$config['url']:'laravel-theme').'/{themeId}', 'ScreenshotController@show')->name($config['name']);
                });

            }
        }
    }

    public function register()
    {
        // 合并配置文件
        $this->mergeConfigFrom(
            realpath(__DIR__ . '/../config/laravel-theme.php'), 'laravel-theme'
        );
        $this->registerTheme();
        $this->registerViewFinder();

    }


    public function registerTheme()
    {
        $this->app->singleton(ThemeManager::class, function ($app) {
            return new ThemeManager($app['files'], $app['config']['laravel-theme']);
        });
    }

    /**
     * Register the view finder implementation.
     *
     * @return void
     */
    public function registerViewFinder()
    {
        $this->app->bind('view.finder', function ($app) {
            return new FileViewFinder($app['files'], $app['config']['view.paths']);
        });
    }

}