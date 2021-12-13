<?php

namespace Artificertech\EloquentAttributeMiddleware\Tests\Console\Commands;

use Artificertech\EloquentAttributeMiddleware\Tests\TestCase;

class MutatorMakeCommandTest extends TestCase
{
    /** @test */
    public function it_creates_middleware_classes()
    {
        $this->artisan('make:mutator TestMutator')->assertExitCode(0);

        $this->assertTrue(file_exists(app_path('Mutators/TestMutator.php')));
    }
}
