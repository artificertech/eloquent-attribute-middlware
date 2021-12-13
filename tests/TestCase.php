<?php

namespace Artificertech\EloquentAttributeMiddleware\Tests;

use Artificertech\EloquentAttributeMiddleware\EloquentAttributeMiddlewareServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
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
            EloquentAttributeMiddlewareServiceProvider::class,
        ];
    }
}
