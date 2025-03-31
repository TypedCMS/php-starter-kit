<?php

declare(strict_types=1);

namespace TypedCMS\PHPStarterKit\Models;

use Illuminate\Support\Arr;

use function str_starts_with;

/**
 * @property-read string $name
 * @property-read string $url
 * @property-read object $conversions
 * @property-read array<string> $conversionsInProgress
 * @property-read string|null $constraintUrl
 */
class Media extends Model
{
    protected $type = 'media';

    public function getConstraintUrlAttribute(): ?string
    {
        return Arr::first(
            (array) $this->conversions,
            static fn (string $url, string $name): bool => str_starts_with($name, 'constraint-'),
        );
    }
}
