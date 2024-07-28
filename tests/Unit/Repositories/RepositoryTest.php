<?php

declare(strict_types=1);

namespace TypedCMS\PHPStarterKit\Tests\Unit\Repositories;

use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Swis\JsonApi\Client\Document;
use Swis\JsonApi\Client\DocumentFactory;
use Swis\JsonApi\Client\Interfaces\DocumentClientInterface;
use Swis\JsonApi\Client\Interfaces\DocumentInterface;
use TypedCMS\PHPStarterKit\StarterKit;
use TypedCMS\PHPStarterKit\Tests\TestCase;
use TypedCMS\PHPStarterKit\Tests\Unit\Repositories\Fakes\Repository;

final class RepositoryTest extends TestCase
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
    public function itUsesTheSpecifiedEndpoint(): void
    {
        /** @var DocumentClientInterface $client */
        $client = $this->mock(DocumentClientInterface::class);

        $repository = new Repository($client, new DocumentFactory());

        $this->assertSame('things', $repository->getSpecifiedEndpoint());
    }

    /**
     * @test
     */
    public function itUsesApiEndpoints(): void
    {
        /** @var DocumentClientInterface $client */
        $client = $this->mock(DocumentClientInterface::class);

        $repository = new Repository($client, new DocumentFactory());

        $this->assertSame($this->getApiEndpoint('things'), $repository->getEndpoint());
        $this->assertSame('things', $repository->getSpecifiedEndpoint());
    }

    /**
     * @test
     */
    public function itUsesMapiEndpoints(): void
    {
        /** @var DocumentClientInterface $client */
        $client = $this->mock(DocumentClientInterface::class);

        $repository = new Repository($client, new DocumentFactory());

        $this->assertSame($this->getMapiEndpoint('things'), $repository->mapi()->getEndpoint());
    }

    /**
     * @test
     */
    public function itGetsAll(): void
    {
        $document = new Document();

        /** @var DocumentClientInterface $client */
        $client = $this->mock(DocumentClientInterface::class,
            function (MockInterface $mock) use ($document) {
                $mock->shouldReceive('get')
                    ->with($this->getApiEndpoint('things?foo=bar&all=1'), [])
                    ->andReturn($document)
                    ->once();
            }
        );

        $repository = new Repository($client, new DocumentFactory());

        $this->assertSame($document, $repository->all(['foo' => 'bar']));
    }

    /**
     * @test
     */
    public function itTakesOne(): void
    {
        $document = new Document();

        /** @var DocumentClientInterface $client */
        $client = $this->mock(DocumentClientInterface::class,
            function (MockInterface $mock) use ($document) {
                $mock->shouldReceive('get')
                    ->with($this->getApiEndpoint('things?foo=bar'), [])
                    ->andReturn($document)
                    ->once();
            }
        );

        $repository = new Repository($client, new DocumentFactory());

        $this->assertSame($document, $repository->take(['foo' => 'bar']));
    }

    /**
     * @test
     */
    public function itFindsOne(): void
    {
        $document = new Document();

        /** @var DocumentClientInterface $client */
        $client = $this->mock(DocumentClientInterface::class,
            function (MockInterface $mock) use ($document) {
                $mock->shouldReceive('get')
                    ->with($this->getApiEndpoint('things/foo?bar=baz'), [])
                    ->andReturn($document)
                    ->once();
            }
        );

        $repository = new Repository($client, new DocumentFactory());

        $this->assertSame($document, $repository->find('foo', ['bar' => 'baz']));
    }

    /**
     * @test
     */
    public function itCanFindOneWithFindOrFail(): void
    {
        $document = new Document();

        /** @var DocumentClientInterface $client */
        $client = $this->mock(DocumentClientInterface::class,
            function (MockInterface $mock) use ($document) {
                $mock->shouldReceive('get')
                    ->with($this->getApiEndpoint('things/foo?bar=baz'), [])
                    ->andReturn($document)
                    ->once();
            }
        );

        $repository = new Repository($client, new DocumentFactory());

        $this->assertSame($document, $repository->findOrFail('foo', ['bar' => 'baz']));
    }

    /**
     * @test
     */
    public function itCanFailWithFindOrFail(): void
    {
        $response = $this->mock(ResponseInterface::class,
            static function (MockInterface $mock) {
                $mock->shouldReceive('getStatusCode')->andReturn(404)->once();
            }
        );

        $document = $this->mock(DocumentInterface::class,
            static function (MockInterface $mock) use ($response) {

                $mock->shouldReceive('hasErrors')->andReturn(true)->once();

                $mock->shouldReceive('getResponse')->andReturn($response)->once();
            }
        );

        /** @var DocumentClientInterface $client */
        $client = $this->mock(DocumentClientInterface::class,
            function (MockInterface $mock) use ($document) {
                $mock->shouldReceive('get')
                    ->with($this->getApiEndpoint('things/foo?bar=baz'))
                    ->andReturn($document)
                    ->once();
            }
        );

        $repository = new Repository($client, new DocumentFactory());

        $this->expectException(RuntimeException::class);

        $repository->findOrFail('foo', ['bar' => 'baz']);
    }

    private function getApiEndpoint(string $append): string
    {
        return "{$this->apiEndpoint}@foo/bar/{$append}";
    }

    private function getMapiEndpoint(string $append): string
    {
        return "{$this->mapiEndpoint}@foo/bar/{$append}";
    }
}
