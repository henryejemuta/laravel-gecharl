<?php

namespace Henryejemuta\LaravelGecharl\Tests;

use Orchestra\Testbench\TestCase;
use Henryejemuta\LaravelGecharl\GecharlServiceProvider;

class ExampleTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [GecharlServiceProvider::class];
    }

    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
