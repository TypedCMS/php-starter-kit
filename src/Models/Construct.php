<?php

declare(strict_types=1);

namespace TypedCMS\PHPStarterKit\Models;

use Swis\JsonApi\Client\Meta;
use UnexpectedValueException;

/**
 * @property string $identifier
 */
class Construct extends Model
{
    /**
     * @var string
     */
    protected $type = 'constructs';

    protected string $blueprint;

    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(
        array $attributes = [],
        private readonly bool $global = false,
    ) {
        parent::__construct($attributes);
    }

    public function isGlobal(): bool
    {
        return $this->global;
    }

    public function getBlueprint(): string
    {
        return $this->blueprint;
    }

    /**
     * @return $this
     */
    public function setMeta(?Meta $meta): static
    {
        if ($meta === null || !isset($meta['type'])) {
            throw new UnexpectedValueException('Construct meta data must contain a type attribute.');
        }

        $this->blueprint = (string) $meta['type'];

        return parent::setMeta($meta);
    }
}
