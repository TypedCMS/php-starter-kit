<?php

declare(strict_types=1);

namespace TypedCMS\PHPStarterKit\Utils;

use function array_key_exists;
use function ctype_lower;
use function preg_replace;

final class Str
{
    /**
     * @var array<string, string>
     */
    private static array $snakeCache = [];

    public static function snake(string $value): string
    {
        $key = $value;

        if (array_key_exists($value, self::$snakeCache)) {
            return self::$snakeCache[$key];
        }

        if (!ctype_lower($value)) {
            $value = self::lower(preg_replace('/(.)(?=[A-Z])/u', '$1-', $value));
        }

        return self::$snakeCache[$key] = $value;
    }

    public static function lower(string $value): string
    {
        return mb_strtolower($value, 'UTF-8');
    }
}
