<?php

namespace Agilize\LaravelDataMapper\Tests;

use Agilize\LaravelDataMapper\DataMappingMiddleware;
use Agilize\LaravelDataMapper\DataMappingServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        $app['config']['datamapping'] = [
            'entity_directory' => __DIR__ . '/Model',
            'primary_key_type' => 'integer',
            'api_version' => 'v1',
        ];
    }

    protected function getPackageProviders($app)
    {
        return [DataMappingServiceProvider::class];
    }

    /**
     * Define environment setup.
     *
     * @param  Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        /** @var Router $router */
        $router = $app['router'];

        $this->addWebRoutes($router);
        $this->addApiRoutes($router);
    }

    /**
     * @param Router $router
     */
    protected function addWebRoutes(Router $router)
    {
        $router->post('web/ping', [
            'uses' => function () {
                return 'Pong';
            }
        ]);
    }

    /**
     * @param Router $router
     */
    protected function addApiRoutes($router)
    {
        $router->get('api/test-user/{id}', [
            'uses' => function (Request $request) {
                return $request->input('test-user');
            }
        ]);

        $router->post('api/test-user/{id}', [
            'uses' => function (Request $request) {
                return $request->input('test-user');
            }
        ]);

        $router->put('api/test-user/{id}', [
            'uses' => function (Request $request) {
                return $request->input('test-user');
            }
        ]);

        $router->get('api/test-user', [
            'uses' => function (Request $request) {
                return $request->input('test-user');
            }
        ]);

        $router->post('api/test-user', [
            'uses' => function (Request $request) {
                return $request->input('test-user');
            }
        ])
            ->middleware(DataMappingMiddleware::class);

    }
}