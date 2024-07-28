<?php

declare(strict_types=1);

namespace TypedCMS\PHPStarterKit\Tests\Unit\Models;

use Swis\JsonApi\Client\Meta;
use TypedCMS\PHPStarterKit\Models\Construct;
use TypedCMS\PHPStarterKit\Tests\TestCase;
use UnexpectedValueException;

final class ConstructTest extends TestCase
{
    /**
     * @test
     */
    public function itHasAConstructsType(): void
    {
        $this->assertEquals('constructs', (new Construct())->getType());
    }

    /**
     * @test
     */
    public function itCanBeAGlobal(): void
    {
        $this->assertTrue((new Construct(global: true))->isGlobal());
    }

    /**
     * @test
     */
    public function itDiscoversTheBlueprintInMeta(): void
    {
        $this->assertEquals('page', (new Construct())
            ->setMeta(new Meta(['type' => 'page']))
            ->getBlueprint(),
        );
    }

    /**
     * @test
     */
    public function itThrowsAnExceptionWhenTypeNotInMeta(): void
    {
        $this->expectException(UnexpectedValueException::class);

        (new Construct())->setMeta(new Meta([]));
    }
}
