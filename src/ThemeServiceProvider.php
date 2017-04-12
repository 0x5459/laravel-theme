<?php


namespace Ty666\LaravelTheme;


use Illuminate\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        $this->publishes([
            realpath(__DIR__.'/../config/theme.php') => config_path('theme.php'),
        ]);
    }

    public function register()
    {
        // 合并配置文件
        $this->mergeConfigFrom(
            realpath(__DIR__.'/../config/theme.php'), 'theme'
        );
        $this->registerTheme();
        $this->registerFactory();
        $this->registerViewFinder();
    }


    public function registerTheme()
    {
        $this->app->singleton(Theme::class, function ($app) {
            return new Theme($app['files'], $app['config']['theme']);
        });
    }

    /**
     * Register the view environment.
     *
     * @return void
     */
    public function registerFactory()
    {
        $this->app->singleton('view', function ($app) {
            // Next we need to grab the engine resolver instance that will be used by the
            // environment. The resolver will be used by an environment to get each of
            // the various engine implementations such as plain PHP or Blade engine.
            $resolver = $app['view.engine.resolver'];

            $finder = $app['view.finder'];

            $env = new Factory($resolver, $finder, $app['events']);

            // We will also set the container instance on this view environment since the
            // view composers may be classes registered in the container, which allows
            // for great testable, flexible composers for the application developer.
            $env->setContainer($app);

            $env->share('app', $app);

            return $env;
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