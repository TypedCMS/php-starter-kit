<?php

declare(strict_types=1);

namespace TypedCMS\PHPStarterKit\Tests\Fixture\Repositories;

use TypedCMS\PHPStarterKit\Repositories\ConstructsRepository;

class FooBarConstructsRepository extends ConstructsRepository
{
    protected string $collection = 'foo-bar-index';

    protected string $blueprint = 'foo';
}
