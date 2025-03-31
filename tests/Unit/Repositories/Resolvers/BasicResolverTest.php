<?php

declare(strict_types=1);

namespace TypedCMS\PHPStarterKit\Tests\Unit\Repositories\Resolvers;

use PHPUnit\Framework\Attributes\Test;
use TypedCMS\PHPStarterKit\Repositories\ConstructsRepository;
use TypedCMS\PHPStarterKit\Repositories\Repository;
use TypedCMS\PHPStarterKit\Repositories\Resolvers\BasicResolver;
use TypedCMS\PHPStarterKit\StarterKit;
use TypedCMS\PHPStarterKit\Tests\Fixture\Repositories\FooRepository;
use TypedCMS\PHPStarterKit\Tests\TestCase;
use UnexpectedValueException;

use function dirname;

final class BasicResolverTest extends TestCase
{
    private BasicResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resolver = new class extends BasicResolver
        {
            protected function getPath(): string
            {
                return dirname(__DIR__, 3).'/Fixture/Repositories';
            }

            protected function getNamespace(): string
            {
                return 'TypedCMS\\PHPStarterKit\\Tests\\Fixture\\Repositories';
            }
        };
    }

    #[Test]
    public function itResolvesRepositoriesByBlueprint(): void
    {
        $repos = $this->resolver->resolveByBlueprint('foo');

        $this->assertCount(2, $repos);

        /** @var ConstructsRepository $repo */
        foreach ($repos as $repo) {

            $this->assertEquals($repo->getBlueprint(), 'foo');
        }
    }

    #[Test]
    public function itResolvesRepositoriesByEndpoint(): void
    {
        $repos = $this->resolver->resolveByEndpoint('bars');

        $this->assertCount(2, $repos);

        /** @var Repository $repo */
        foreach ($repos as $repo) {

            $this->assertEquals($repo->getSpecifiedEndpoint(), 'bars');
        }
    }

    #[Test]
    public function itThrowsAnExceptionWhenEncounteringInvalidRepos(): void
    {
        $this->expectException(UnexpectedValueException::class);

        StarterKit::container()->instance(FooRepository::class, new class {});

        $this->resolver->resolveByEndpoint('foos');
    }

    #[Test]
    public function itResolvesWhenThePathDoesNotExist(): void
    {
        $resolver = new class extends BasicResolver
        {
            protected function getPath(): string
            {
                return dirname(__DIR__, 3).'/Fixture/NotRepositories';
            }

            protected function getNamespace(): string
            {
                return 'TypedCMS\\PHPStarterKit\\Tests\\Fixture\\Repositories';
            }
        };

        $repos = $resolver->resolveByBlueprint('foo');

        $this->assertCount(0, $repos);
    }
}
