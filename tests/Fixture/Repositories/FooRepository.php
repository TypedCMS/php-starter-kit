<?php

declare(strict_types=1);

namespace TypedCMS\PHPStarterKit\Tests\Fixture\Repositories;

use TypedCMS\PHPStarterKit\Repositories\Repository;

class FooRepository extends Repository
{
    protected $endpoint = 'foos';
}
