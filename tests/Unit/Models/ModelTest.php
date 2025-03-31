<?php

declare(strict_types=1);

namespace TypedCMS\PHPStarterKit\Tests\Unit\Models;

use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;
use TypedCMS\PHPStarterKit\Models\Model;
use TypedCMS\PHPStarterKit\Tests\TestCase;

final class ModelTest extends TestCase
{
    #[Test]
    public function itReturnACarbonInstanceForTheCreatedAttribute(): void
    {
        $model = new Model(['created' => '2024-07-28']);

        $this->assertInstanceOf(Carbon::class, $model->created);
        $this->assertEquals('2024-07-28', $model->created->format('Y-m-d'));
    }

    #[Test]
    public function itReturnACarbonInstanceForTheUpdatedAttribute(): void
    {
        $model = new Model(['updated' => '2024-07-28']);

        $this->assertInstanceOf(Carbon::class, $model->updated);
        $this->assertEquals('2024-07-28', $model->updated->format('Y-m-d'));
    }

    #[Test]
    public function itCovertsCamelAttributesToSnake(): void
    {
        $model = new Model(['foo-bar' => 'baz']);

        $this->assertEquals('baz', $model->fooBar);
    }

    #[Test]
    public function itDoesNotCovertsCamelAttributesToSnakeWhenCamelExists(): void
    {
        $model = new Model([
            'foo-bar' => 'baz',
            'fooBar' => 'barBaz',
        ]);

        $this->assertEquals('barBaz', $model->fooBar);
    }
}
