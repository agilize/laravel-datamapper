<?php

namespace Agilize\LaravelDataMapper\Tests\ServiceProvider;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(realpath(__DIR__.'/../database/migrations'));
    }
}