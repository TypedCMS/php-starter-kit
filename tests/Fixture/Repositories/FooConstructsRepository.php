<?php

declare(strict_types=1);

namespace TypedCMS\PHPStarterKit\Tests\Fixture\Repositories;

use TypedCMS\PHPStarterKit\Repositories\ConstructsRepository;

class FooConstructsRepository extends ConstructsRepository
{
    protected string $collection = 'foo-index';

    protected string $blueprint = 'foo';
}
