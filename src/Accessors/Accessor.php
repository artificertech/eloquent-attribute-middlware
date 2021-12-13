<?php

namespace Artificertech\EloquentAttributeMiddleware\Accessors;

use Closure;

abstract class Accessor
{
    abstract public function __invoke($key, $model, Closure $next);
}
