<?php

declare(strict_types=1);

namespace TypedCMS\PHPStarterKit\Tests\Unit\Repositories\Fakes;

use TypedCMS\PHPStarterKit\Repositories\ConstructsRepository as BaseConstructsRepository;

class ConstructsRepository extends BaseConstructsRepository
{
    protected string $collection = 'things';

    protected string $blueprint = 'thing';
}
