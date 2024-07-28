<?php

declare(strict_types=1);

namespace TypedCMS\PHPStarterKit\Tests\Unit\Repositories;

use Swis\JsonApi\Client\DocumentFactory;
use Swis\JsonApi\Client\Interfaces\DocumentClientInterface;
use TypedCMS\PHPStarterKit\Repositories\Repository;
use TypedCMS\PHPStarterKit\StarterKit;
use TypedCMS\PHPStarterKit\Tests\TestCase;
use TypedCMS\PHPStarterKit\Tests\Unit\Repositories\Fakes\ConstructsRepository;

final class ConstructsRepositoryTest extends TestCase
{
    private string $apiEndpoint;

    private string $mapiEndpoint;

    public function setUp(): void
    {
        $this->apiEndpoint = Repository::$apiEndpoint;
        $this->mapiEndpoint = Repository::$mapiEndpoint;

        StarterKit::configure(['base_uri' => '@foo/bar']);
    }

    /**
     * @test
     */
    public function itUsesApiEndpointsViaCollections(): void
    {
        /** @var DocumentClientInterface $client */
        $client = $this->mock(DocumentClientInterface::class);

        $repository = new ConstructsRepository($client, new DocumentFactory());

        $this->assertSame($this->apiEndpoint.'@foo/bar/things', $repository->getEndpoint());
    }

    /**
     * @test
     */
    public function itUsesMapiEndpointsWithBlueprint(): void
    {
        /** @var DocumentClientInterface $client */
        $client = $this->mock(DocumentClientInterface::class);

        $repository = new ConstructsRepository($client, new DocumentFactory());

        $this->assertSame($this->mapiEndpoint.'@foo/bar/constructs/thing', $repository->mapi()->getEndpoint());
    }
}
