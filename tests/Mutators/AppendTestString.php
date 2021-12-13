<?php

namespace Artificertech\EloquentAttributeMiddleware\Tests\Mutators;

use Artificertech\EloquentAttributeMiddleware\Mutators\Mutator;
use Attribute;
use Closure;

#[Attribute(Attribute::TARGET_METHOD)]
class AppendTestString extends Mutator
{
    /**
     * Run the mutator on the specified model attribute value
     *
     * @return mixed
     */
    public function __invoke($value, $key, $model, Closure $next)
    {
        return $next($value . '_test');
    }
}
