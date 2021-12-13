<?php

namespace Artificertech\EloquentAttributeMiddleware\Models\Concerns;

use Artificertech\EloquentAttributeMiddleware\Accessors\Accessor;
use Artificertech\EloquentAttributeMiddleware\Mutators\Mutator;
use Illuminate\Support\Str;

trait HasAttributeMiddleware
{
    /**
     * Get the value of an attribute using its mutator.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function mutateAttribute($key, $value)
    {
        $accessorMethod = 'get' . Str::studly($key) . 'Attribute';

        $accessorAttributes = (new \ReflectionMethod($this, $accessorMethod))->getAttributes(Accessor::class, 2);

        $model = $this;

        $pipeline = array_reduce(
            array_reverse($accessorAttributes),
            function ($nextClosure, \ReflectionAttribute $accessorAttribute) use ($key, $model) {
                return function () use ($nextClosure, $accessorAttribute, $key, $model) {
                    return $accessorAttribute->newInstance()($key, $model, $nextClosure);
                };
            },
            function () use ($value, $accessorMethod) {
                return $this->$accessorMethod($value);
            }
        );

        return $pipeline();
    }

    /**
     * Set the value of an attribute using its mutator.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function setMutatedAttributeValue($key, $value)
    {
        $mutatorMethod = 'set' . Str::studly($key) . 'Attribute';

        $mutatorAttributes = (new \ReflectionMethod($this, $mutatorMethod))->getAttributes(Mutator::class, 2);

        $model = $this;

        $pipeline = array_reduce(
            array_reverse($mutatorAttributes),
            function ($nextClosure, \ReflectionAttribute $mutatorAttribute) use ($key, $model) {
                return function ($value) use ($nextClosure, $mutatorAttribute, $key, $model) {
                    return $mutatorAttribute->newInstance()($value, $key, $model, $nextClosure);
                };
            },
            function ($value) {
                return $value;
            }
        );

        return $this->$mutatorMethod($pipeline($value));
    }
}
