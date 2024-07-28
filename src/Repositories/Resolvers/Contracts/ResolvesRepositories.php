<?php

declare(strict_types=1);

namespace TypedCMS\PHPStarterKit\Repositories\Resolvers\Contracts;

use TypedCMS\PHPStarterKit\Repositories\Contracts\CollectsConstructs;
use TypedCMS\PHPStarterKit\Repositories\Repository;

interface ResolvesRepositories
{
    /**
     * @return array<Repository&CollectsConstructs>
     */
    public function resolveByBlueprint(string $blueprint): array;

    /**
     * @return array<Repository>
     */
    public function resolveByEndpoint(string $endpoint): array;
}
