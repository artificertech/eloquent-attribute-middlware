<?php

namespace Artificertech\EloquentAttributeMiddleware\Tests\Models\Concerns;

use Artificertech\EloquentAttributeMiddleware\Tests\Models\User;
use Artificertech\EloquentAttributeMiddleware\Tests\TestCase;

class HasAttributeMiddleware extends TestCase
{
    /** @test */
    public function it_has_accessor_middleware()
    {
        $user = new User(['name' => 'Cole Shirley']);

        $this->assertEquals('Cole Shirley', $user->name);
        $this->assertEquals('COLE SHIRLEY', $user->upperName);
        $this->assertEquals('Cole Shirley_test', $user->appendTestName);
        $this->assertEquals('COLE SHIRLEY_TEST', $user->upperAppendTestName);
        $this->assertEquals('COLE SHIRLEY_test', $user->appendTestUpperName);
    }

    /** @test */
    public function it_has_mutator_middleware()
    {
        $user = new User([
            'upperName' => 'Cole Shirley',
            'appendTestName' => 'Cole Shirley',
            'upperAppendTestName' => 'Cole Shirley',
            'appendTestUpperName' => 'Cole Shirley',
        ]);

        $this->assertEquals('COLE SHIRLEY', $user->getAttributes()['upperName']);
        $this->assertEquals('Cole Shirley_test', $user->getAttributes()['appendTestName']);
        $this->assertEquals('COLE SHIRLEY_test', $user->getAttributes()['upperAppendTestName']);
        $this->assertEquals('COLE SHIRLEY_TEST', $user->getAttributes()['appendTestUpperName']);
    }
}
