<?php

namespace Artificertech\EloquentAttributeMiddleware\Tests\Console\Commands;

use Artificertech\EloquentAttributeMiddleware\Tests\TestCase;

class AccessorMakeCommandTest extends TestCase
{
    /** @test */
    public function it_creates_middleware_classes()
    {
        $this->artisan('make:accessor TestAccessor')->assertExitCode(0);

        $this->assertTrue(file_exists(app_path('Accessors/TestAccessor.php')));
    }
}
