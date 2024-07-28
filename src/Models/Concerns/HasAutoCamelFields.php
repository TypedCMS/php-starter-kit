<?php

declare(strict_types=1);

namespace TypedCMS\PHPStarterKit\Models\Concerns;

use TypedCMS\PHPStarterKit\Utils\Str;

use function array_key_exists;

trait HasAutoCamelFields
{
    /**
     * @param string $key
     */
    public function getAttribute($key): mixed
    {
        if (
            !$this->attributeExists($key) &&
            $this->attributeExists(Str::snake($key))
        ) {
            return parent::getAttribute(Str::snake($key));
        }

        return parent::getAttribute($key);
    }

    private function attributeExists(string $key): bool
    {
        return array_key_exists($key, $this->attributes)
            || array_key_exists($key, $this->casts)
            || $this->hasGetMutator($key);
    }
}
