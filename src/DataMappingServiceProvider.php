<?php

namespace Agilize\LaravelDataMapper;

use Illuminate\Support\ServiceProvider;

class DataMappingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom($this->configPath(), 'datamapping');

        $this->app->singleton(
            DataMappingMiddleware::class,
            function ($app) {
                return new DataMappingMiddleware($this->dataMappingOptions());
            }
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes(
            [$this->configPath() => config_path('datamapping.php')],
            'datamapping'
        );
    }

    /**
     * Set the config path
     *
     * @return string
     */
    protected function configPath()
    {
        return __DIR__ . '/../config/datamapping.php';
    }

    /**
     * Get options for DataMappingMiddleware
     *
     * @return array
     */
    protected function dataMappingOptions()
    {
        $config = $this->app['config']->get('datamapping');

        foreach (['entity_directory', 'primary_key_type', 'api_version'] as $key) {
            if (!is_string($config[$key])) {
                throw new \RuntimeException('DataMapping config `' . $key . '` should be a string.');
            }
        }

        return [
            'entityDirectory' => $config['entity_directory'],
            'primaryKeyType' => $config['primary_key_type'],
            'apiVersion' => $config['api_version'],
        ];
    }
}
