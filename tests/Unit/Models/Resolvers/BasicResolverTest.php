<?php

declare(strict_types=1);

namespace TypedCMS\PHPStarterKit\Tests\Unit\Models\Resolvers;

use TypedCMS\PHPStarterKit\StarterKit;
use TypedCMS\PHPStarterKit\Tests\Fixture\Models\Foo;
use TypedCMS\PHPStarterKit\Tests\Fixture\Models\FooConstruct;
use TypedCMS\PHPStarterKit\Models\Construct;
use TypedCMS\PHPStarterKit\Models\Resolvers\BasicResolver;
use TypedCMS\PHPStarterKit\Tests\TestCase;

use function dirname;

final class BasicResolverTest extends TestCase
{
    private BasicResolver $resolver;

    public function setUp(): void
    {
        parent::setUp();

        $this->resolver = new class extends BasicResolver {

            protected function getPath(): string
            {
                return dirname(__DIR__, 3) . '/Fixture/Models';
            }

            protected function getNamespace(): string
            {
                return 'TypedCMS\\PHPStarterKit\\Tests\\Fixture\\Models';
            }
        };
    }

    /**
     * @test
     */
    public function itResolvesAModelByResourceType(): void
    {
        $model = $this->resolver->resolve('bars');

        $this->assertEquals($model->getType(), 'bars');
    }

    /**
     * @test
     */
    public function itResolvesNullByResourceTypeForModelsThatDontExist(): void
    {
        $this->assertNull($this->resolver->resolve('baz'));
    }

    /**
     * @test
     */
    public function itResolvesTheDefaultConstructModelForConstructs(): void
    {
        $model = $this->resolver->resolve('constructs');

        $this->assertInstanceOf(Construct::class, $model);
    }

    /**
     * @test
     */
    public function itResolvesTheDefaultConstructModelForGlobals(): void
    {
        /** @var Construct $model */
        $model = $this->resolver->resolve('globals');

        $this->assertInstanceOf(Construct::class, $model);
    }

    /**
     * @test
     */
    public function itResolvesASpecialisedConstructModelByResourceTypePath(): void
    {
        $model = $this->resolver->resolve('constructs:foo');

        $this->assertInstanceOf(FooConstruct::class, $model);
    }

    /**
     * @test
     */
    public function itResolvesAGenericConstructModelByResourceTypePath(): void
    {
        $model = $this->resolver->resolve('constructs:baz');

        $this->assertInstanceOf(Construct::class, $model);
    }

    /**
     * @test
     */
    public function itResolvesASpecialisedGlobalModelByResourceTypePath(): void
    {
        /** @var FooConstruct $model */
        $model = $this->resolver->resolve('globals:foo');

        $this->assertInstanceOf(FooConstruct::class, $model);

        $this->assertTrue($model->isGlobal());
    }

    /**
     * @test
     */
    public function itResolvesAGenericGlobalsModelByResourceTypePath(): void
    {
        /** @var Construct $model */
        $model = $this->resolver->resolve('globals:baz');

        $this->assertInstanceOf(Construct::class, $model);

        $this->assertTrue($model->isGlobal());
    }

    /**
     * @test
     */
    public function itSkipsInvalidModels(): void
    {
        StarterKit::container()->instance(Foo::class, new class {});

        $this->assertNull($this->resolver->resolve('foo'));
    }

    /**
     * @test
     */
    public function itResolvesWhenThePathDoesNotExist(): void
    {
        $resolver = new class extends BasicResolver {

            protected function getPath(): string
            {
                return dirname(__DIR__, 3) . '/Fixture/NotModels';
            }

            protected function getNamespace(): string
            {
                return 'TypedCMS\\PHPStarterKit\\Tests\\Fixture\\Models';
            }
        };

        $model = $resolver->resolve('constructs:baz');

        $this->assertInstanceOf(Construct::class, $model);
    }
}
