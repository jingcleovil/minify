<?php namespace Jinggo\Minify;

use Illuminate\Support\ServiceProvider;

class MinifyServiceProvider extends ServiceProvider {

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
		$this->package('jinggo/minify');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{

		$this->app['minify'] = $this->app->share(function($app)
        {
            return new Minify;
        });
		
		$this->app->booting(function()
        {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('Minify', 'Jinggo\Minify\Facades\Minify');
        });
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('minify');
	}

}