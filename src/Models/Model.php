<?php

declare(strict_types=1);

namespace TypedCMS\PHPStarterKit\Models;

use Carbon\Carbon;
use Swis\JsonApi\Client\Item;
use TypedCMS\PHPStarterKit\Models\Concerns\HasAutoCamelFields;

/**
 * @property string $id
 * @property Carbon $created
 * @property Carbon $updated
 */
class Model extends Item
{
    use HasAutoCamelFields;

    public function getCreatedAttribute(): Carbon
    {
        return Carbon::parse($this->attributes['created']);
    }

    public function getUpdatedAttribute(): Carbon
    {
        return Carbon::parse($this->attributes['updated']);
    }
}

