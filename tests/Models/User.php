<?php

namespace Artificertech\EloquentAttributeMiddleware\Tests\Models;

use Artificertech\EloquentAttributeMiddleware\Models\Concerns\HasAttributeMiddleware;
use Artificertech\EloquentAttributeMiddleware\Tests\Accessors\AppendTestString as AppendTestStringAccessor;
use Artificertech\EloquentAttributeMiddleware\Tests\Accessors\Upper as UpperAccessor;
use Artificertech\EloquentAttributeMiddleware\Tests\Mutators\AppendTestString as AppendTestStringMutator;
use Artificertech\EloquentAttributeMiddleware\Tests\Mutators\Upper as UpperMutator;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasAttributeMiddleware;

    protected $fillable = [
        'name',
        'upperName',
        'appendTestName',
        'upperAppendTestName',
        'appendTestUpperName',
    ];

    /**
     * Accessors.
     */
    public function getNameAttribute($value)
    {
        return $value;
    }

    #[UpperAccessor]
    public function getUpperNameAttribute()
    {
        return $this->name;
    }

    #[AppendTestStringAccessor]
    public function getAppendTestNameAttribute()
    {
        return $this->name;
    }

    #[UpperAccessor]
    #[AppendTestStringAccessor]
    public function getUpperAppendTestNameAttribute()
    {
        return $this->name;
    }

    #[AppendTestStringAccessor]
    #[UpperAccessor]
    public function getAppendTestUpperNameAttribute()
    {
        return $this->name;
    }

    /**
     * MUTATORS.
     */
    #[UpperMutator]
    public function setUpperNameAttribute($value)
    {
        $this->attributes['upperName'] = $value;
    }

    #[AppendTestStringMutator]
    public function setAppendTestNameAttribute($value)
    {
        $this->attributes['appendTestName'] = $value;
    }

    #[UpperMutator]
    #[AppendTestStringMutator]
    public function setUpperAppendTestNameAttribute($value)
    {
        $this->attributes['upperAppendTestName'] = $value;
    }

    #[AppendTestStringMutator]
    #[UpperMutator]
    public function setAppendTestUpperNameAttribute($value)
    {
        $this->attributes['appendTestUpperName'] = $value;
    }
}
