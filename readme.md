# EloquentAttributeMiddleware

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

This package enables you to define middleware classes for your Eloquent model accessors and mutators using php 8 Attributes. This allows you to reuse complex code for your computed attributes. Take a look at [contributing.md](contributing.md) to see a to do list.

## Requirements
php ^8.0, Laravel ^8

## Installation

Via Composer

``` bash
composer require artificertech/eloquent-attribute-middleware
```

## Usage

### Accessors

create your accessor middleware class using laravel artisan 

``` bash
php artisan make:accessor MyAccessor
```

Configure your accessor middleware. Accessor middlware should modify the response of the $next() callback and return the modified value.
``` php
namespace App\Accessors;

use Artificertech\EloquentAttributeMiddleware\Accessors\Accessor;
use Attribute;
use Closure;

#[Attribute(Attribute::TARGET_METHOD)]
class Upper extends Accessor
{
    /**
     * Run the mutator on the specified model attribute value
     *
     * @param $key the attribute name
     * @param $model the model this attribute is being set for
     * @param $next the next middleware function to call
     * @return mixed
     */
    public function __invoke($key, $model, Closure $next)
    {
        return Str::upper($next());
    }
}

```

Add the middleware functionality to your Eloquent Model

``` php

...
use App\Accessors\Upper;
use Artificertech\EloquentAttributeMiddleware\Models\Concerns\HasAttributeMiddleware;
...
class User extends Model
{
    use HasAttributeMiddleware;
    ...

    #[Upper]
    public function getNameAttribute($value)
    {
        return $value;
    }
}
```

Now any time you retrieve the name attribute it will be Uppercase

#### Execution Order

Accessors run in order of definition. In the following example the user 'name' attribute is stored in the database as 'Cole Shirley'

``` php
#[Attribute(Attribute::TARGET_METHOD)]
class Upper extends Accessor
{
    /**
     * make the value uppercase
     *
     * @param $key the attribute name
     * @param $model the model this attribute is being set for
     * @param $next the next middleware function to call
     * @return mixed
     */
    public function __invoke($key, $model, Closure $next)
    {
        return Str::upper($next());
    }
}
...
#[Attribute(Attribute::TARGET_METHOD)]
class AppendTestString extends Accessor
{
    /**
     * append _test to the value
     * 
     * @param $key the attribute name
     * @param $model the model this attribute is being set for
     * @param $next the next middleware function to call
     * @return mixed
     */
    public function __invoke($key, $model, Closure $next)
    {
        return $next() . '_test';
    }
}
...
use App\Accessors\Upper;
use App\Accessors\AppendTestString;
use Artificertech\EloquentAttributeMiddleware\Models\Concerns\HasAttributeMiddleware;
...
class User extends Model
{
    use HasAttributeMiddleware;
    ...

    #[Upper]
    #[AppendTestString]
    public function getNameAttribute($value)
    {
        return $value;
    }
}

$user =  User::find(1);

$user->name; // 'COLE SHIRLEY_TEST'
```

Execution order:

1. the Upper __invoke method is called first which retrieves the value of the next callback
1. the AppendTestString __invoke method is then called which retireves the value of the next callback
1. the getNameAttribute method is called with the value 'Cole Shirley' from the stored model attributes
1. that value is passed back to AppendTestString which then concatenates '_test' onto the value
1. the modified string is passed back to Upper which makes the entire string uppercase
1. the finalized string is passed back to the implemenation

### Mutators

create your mutator middleware class using laravel artisan 

``` bash
php artisan make:mutator MyMutator
```

Configure your mutator middleware. Mutator middlware should modify $value parameter before passing it to the $next() callback
``` php
namespace App\Mutators;

use Artificertech\EloquentAttributeMiddleware\Mutators\Mutator;
use Attribute;
use Closure;

#[Attribute(Attribute::TARGET_METHOD)]
class Lower extends Mutator
{
    /**
     * make the value lowercase
     *
     * @param $value the value of the attribute to set
     * @param $key the attribute name
     * @param $model the model this attribute is being set for
     * @param $next the next middleware function to call
     * @return mixed
     */
    public function __invoke($value, $key, $model, Closure $next)
    {
        return $next(Str::lower($value));
    }
}

```

Add the middleware functionality to your Eloquent Model

``` php

...
use App\Mutators\Lower;
use Artificertech\EloquentAttributeMiddleware\Models\Concerns\HasAttributeMiddleware;
...
class User extends Model
{
    use HasAttributeMiddleware;
    ...

    #[Lower]
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
    }
}
```

Now when you set the name attribute it will be lowercased

#### Execution Order

Mutators run in order of definition

``` php
class Lower extends Mutator
{
    /**
     * make the value lowercase
     *
     * @param $value the value of the attribute to set
     * @param $key the attribute name
     * @param $model the model this attribute is being set for
     * @param $next the next middleware function to call
     * @return mixed
     */
    public function __invoke($value, $key, $model, Closure $next)
    {
        return $next(Str::lower($value));
    }
}
...
class WithoutExtraWhitespace extends Mutator
{
    /**
     * make the value lowercase
     *
     * @param $value the value of the attribute to set
     * @param $key the attribute name
     * @param $model the model this attribute is being set for
     * @param $next the next middleware function to call
     * @return mixed
     */
    public function __invoke($value, $key, $model, Closure $next)
    {
        return $next(preg_replace('/\s+/', ' ', $value));
    }
}
...
use App\Mutators\Lower;
use App\Mutators\WithoutExtraWhitespace;
use Artificertech\EloquentAttributeMiddleware\Models\Concerns\HasAttributeMiddleware;
...
class User extends Model
{
    use HasAttributeMiddleware;
    ...

    #[Lower]
    #[WithoutExtraWhitespace]
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
    }
}

$user = new User;

$user->name = 'Cole   Shirley'; // stored as 'cole shirley'
```

Execution order:

1. the Lower __invoke method is called first with the value 'Cole   Shirley'
1. the WithoutExtraWhitespace __invoke method is called with the value 'cole   shirley'
1. the setNameAttribute is called with the value 'cole shirley'
1. if the setNameAttribute has a return value it is passed back to the implementation

## Practical example: Caching model info from api

For most situations you should be able to use normal accessor and mutator functionality. However if you find yourself setting up complicated accessors or mutators repeatedly you may consider extracting that functionality into accessor and mutator middleware. A great example is if you want to cache data related to a model from an external api

``` php
namespace App\Mutators;

use Artificertech\EloquentAttributeMiddleware\Mutators\Mutator;
use Attribute;
use Closure;

#[Attribute(Attribute::TARGET_METHOD)]
class Cached extends Mutator
{
    /**
     * Check the cache for the attribute 
     * 
     * @param $key the attribute name
     * @param $model the model this attribute is being set for
     * @param $next the next middleware function to call
     * @return mixed
     */
    public function __invoke($key, $model, Closure $next)
    {
        return Cache::rememberForever($model::class . ":{$model->getKey()}:{$key}", function () use ($value, $next) {
            return $next();
        });
    }
}

...
namespace App\Models;

use App\Mutators\Cached;
use Artificertech\EloquentAttributeMiddleware\Models\Concerns\HasAttributeMiddleware;
...
class User extends Model
{
    use HasAttributeMiddleware;
    ...

    #[Cached]
    public function getApiDataAttribute()
    {
        return Http::get('https://example.com/api/users/', ['name' => $this->name]);
    }
}
```

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
composer test
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email author@email.com instead of using the issue tracker.

## Credits

- [Cole Shirley][link-author]
- [All Contributors][link-contributors]

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/artificertech/eloquent-attribute-middleware.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/artificertech/eloquent-attribute-middleware.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/artificertech/eloquent-attribute-middleware/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/437927180/shield

[link-packagist]: https://packagist.org/packages/artificertech/eloquent-attribute-middleware
[link-downloads]: https://packagist.org/packages/artificertech/eloquent-attribute-middleware
[link-travis]: https://travis-ci.org/artificertech/eloquent-attribute-middleware
[link-styleci]: https://styleci.io/repos/437927180
[link-author]: https://github.com/coleshirley
[link-contributors]: ../../contributors
