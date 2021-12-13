<?php

namespace Artificertech\EloquentAttributeMiddleware\Tests\Mutators;

use Artificertech\EloquentAttributeMiddleware\Mutators\Mutator;
use Attribute;
use Closure;
use Illuminate\Support\Str;

#[Attribute(Attribute::TARGET_METHOD)]
class Upper extends Mutator
{
    /**
     * Run the mutator on the specified model attribute value.
     *
     * @return mixed
     */
    public function __invoke($value, $key, $model, Closure $next)
    {
        return $next(Str::upper($value));
    }
}
