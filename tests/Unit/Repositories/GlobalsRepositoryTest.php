<?php

declare(strict_types=1);

namespace TypedCMS\PHPStarterKit\Tests\Unit\Repositories;

use Swis\JsonApi\Client\DocumentFactory;
use Swis\JsonApi\Client\Interfaces\DocumentClientInterface;
use TypedCMS\PHPStarterKit\Tests\TestCase;
use TypedCMS\PHPStarterKit\Tests\Unit\Repositories\Fakes\GlobalsRepository;

final class GlobalsRepositoryTest extends TestCase
{
    /**
     * @test
     */
    public function itUsesTheGlobalsEndpoint(): void
    {
        /** @var DocumentClientInterface $client */
        $client = $this->mock(DocumentClientInterface::class);

        $repository = new GlobalsRepository($client, new DocumentFactory());

        $this->assertSame('globals', $repository->getSpecifiedEndpoint());
    }
}
