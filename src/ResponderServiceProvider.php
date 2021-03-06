<?php

namespace Flugg\Responder;

use Flugg\Responder\Console\MakeTransformer;
use Flugg\Responder\Contracts\Manager as ManagerContract;
use Flugg\Responder\Contracts\Responder as ResponderContract;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use League\Fractal\Manager;

/**
 * The Laravel Responder service provider. This is where the package is bootstrapped.
 *
 * @package Laravel Responder
 * @author  Alexander Tømmerås <flugged@gmail.com>
 * @license The MIT License
 */
class ResponderServiceProvider extends BaseServiceProvider
{
    /**
     * Keeps a short reference to the package configurations.
     *
     * @var array
     */
    protected $config;

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
        $this->publishes( [
            __DIR__ . '/../resources/config/responder.php' => config_path( 'responder.php' )
        ], 'config' );

        $this->publishes( [
            __DIR__ . '/../resources/lang/en/errors.php' => resource_path( 'lang/en/errors.php' )
        ], 'lang' );

        $this->commands( [
            MakeTransformer::class
        ] );
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom( __DIR__ . '/../resources/config/responder.php', 'responder' );
        $this->config = $this->app[ 'config' ]->get( 'responder' );

        $this->app->singleton( ResponderContract::class, function () {
            return new Responder();
        } );

        $this->app->singleton( ManagerContract::class, function () {
            return ( new Manager() )->setSerializer( new $this->config[ 'serializer' ] );
        } );

        include __DIR__ . '/helpers.php';
    }
}