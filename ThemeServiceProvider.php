<?php

namespace Litepie\Theme;

use Illuminate\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishResources();
        
        foreach(config('theme.themes') as $key => $val) {
            $this->loadViewsFrom(public_path(config('theme.themeDir').'/'.config("theme.themes.{$key}.theme").'/views'), $key);
        }
    }

    /**
     * Register service provider.
     *
     * @return void
     */
    public function register()
    {

        
        $this->app->singleton('view.finder', function($app) {
            return new \Litepie\Theme\ThemeViewFinder(
                $app['files'],
                $app['config']['view.paths'],
                null
            );
        });
        // Register providers.
        $this->registerAsset();
        $this->registerTheme();
    }

    /**
     * Register asset provider.
     *
     * @return void
     */
    public function registerAsset()
    {
        $this->app->singleton('asset', function ($app) {
            return new Asset();
        });
    }

    /**
     * Register theme provider.
     *
     * @return void
     */
    public function registerTheme()
    {
        $this->app->singleton('theme', function ($app) {
            return new Theme($app['config'], $app['events'], $app['view'], $app['asset'], $app['files']);
        });

       // $this->app->alias('theme', 'Litepie\Theme\Contracts\Theme');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['asset', 'theme'];
    }

    /**
     * Publish resources.
     *
     * @return void
     */
    private function publishResources()
    {
        // Publish configuration file
        $this->publishes([__DIR__ . '/config.php' => config_path('theme.php')], 'config');

    }
}
