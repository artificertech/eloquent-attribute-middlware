<?php

namespace Artificertech\EloquentAttributeMiddleware\Mutators;

use Closure;

abstract class Mutator
{
    abstract public function __invoke($value, $key, $model, Closure $next);
}
