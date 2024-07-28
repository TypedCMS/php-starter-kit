<?php

declare(strict_types=1);

namespace TypedCMS\PHPStarterKit\Tests\Unit\Repositories\Fakes;

use TypedCMS\PHPStarterKit\Repositories\Repository as BaseRepository;

class Repository extends BaseRepository
{
    protected $endpoint = 'things';
}
