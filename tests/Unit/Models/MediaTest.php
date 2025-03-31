<?php

declare(strict_types=1);

namespace TypedCMS\PHPStarterKit\Tests\Unit\Models;

use PHPUnit\Framework\Attributes\Test;
use TypedCMS\PHPStarterKit\Models\Media;
use TypedCMS\PHPStarterKit\Tests\TestCase;

final class MediaTest extends TestCase
{
    #[Test]
    public function itHaAMediaType(): void
    {
        $this->assertEquals('media', (new Media)->getType());
    }

    #[Test]
    public function itAutomaticallyLocatesTheFieldConstraint(): void
    {
        $model = new Media(['conversions' => [
            'url' => 'https://foo.bar/image.webp',
            'thumbnail' => 'https://foo.bar/image-thumbnail.webp',
            'constraint-888' => 'https://foo.bar/image-constraint-888.webp',
        ]]);

        $this->assertEquals('https://foo.bar/image-constraint-888.webp', $model->constraint_url);
    }

    #[Test]
    public function itReturnsNullWithoutAFieldConstraint(): void
    {
        $model = new Media(['conversions' => [
            'url' => 'https://foo.bar/image.webp',
            'thumbnail' => 'https://foo.bar/image-thumbnail.webp',
        ]]);

        $this->assertNull($model->constraint_url);
    }
}
