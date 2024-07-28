<?php

declare(strict_types=1);

namespace TypedCMS\PHPStarterKit\Tests;

use Closure;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use TypedCMS\PHPStarterKit\StarterKit;

use function array_filter;
use function func_get_args;

abstract class TestCase extends PHPUnitTestCase
{
    protected function mock(string $abstract, Closure $mock = null): MockInterface
    {
        return StarterKit::container()->instance($abstract, Mockery::mock(...array_filter(func_get_args())));
    }
}
