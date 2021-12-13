<?php

namespace Artificertech\EloquentAttributeMiddleware\Tests\Accessors;

use Artificertech\EloquentAttributeMiddleware\Accessors\Accessor;
use Attribute;
use Closure;

#[Attribute(Attribute::TARGET_METHOD)]
class AppendTestString extends Accessor
{
    /**
     * Run the mutator on the specified model attribute value
     *
     * @return mixed
     */
    public function __invoke($key, $model, Closure $next)
    {
        return $next() . '_test';
    }
}
