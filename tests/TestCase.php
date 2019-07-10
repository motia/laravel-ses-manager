<?php

namespace Motia\LaravelSesManager\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Motia\LaravelSesManager\Controllers\SESWebhookController;

class TestCase extends Orchestra
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate', ['--database' => 'testbench'])->run();

        $router = $this->app->get('router');
        $router->group([
            'prefix' => 'api/webhooks/ses',
        ], function () use ($router) {
            $router->post('bounce', SESWebhookController::class.'@bounce');
            $router->post('complaint', SESWebhookController::class.'@complaint');
        });
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \Motia\LaravelSesManager\LaravelSesManagerServiceProvider::class,
        ];
    }
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application   $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'base64:7nk3KqPj/W5WMCNfsVrFTJUoCRBbnLluC+fe4nikeYA=');
        $app['config']->set('app.debug', true);

        // Setup default database to use sqlite :memory:
	    $app['config']->set('database.default', 'testbench');
	    $app['config']->set('database.connections.testbench', [
	        'driver'   => 'sqlite',
	        'database' => ':memory:',
	        'prefix'   => '',
	    ]);
    }
}