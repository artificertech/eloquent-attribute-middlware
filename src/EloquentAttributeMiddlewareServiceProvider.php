<?php

namespace Artificertech\EloquentAttributeMiddleware;

use Artificertech\EloquentAttributeMiddleware\Console\Commands\AccessorMakeCommand;
use Artificertech\EloquentAttributeMiddleware\Console\Commands\MutatorMakeCommand;
use Illuminate\Support\ServiceProvider;

class EloquentAttributeMiddlewareServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Registering package commands.
        $this->commands([
            AccessorMakeCommand::class,
            MutatorMakeCommand::class,
        ]);
    }
}
