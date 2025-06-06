<?php

declare(strict_types=1);

namespace TypedCMS\PHPStarterKit;

use Swis\JsonApi\Client\Interfaces\ItemInterface;
use Swis\JsonApi\Client\Parsers\ItemParser as BaseItemParser;

use function is_array;
use function is_object;
use function property_exists;

class ItemParser extends BaseItemParser
{
    public function parse($data): ItemInterface
    {
        if (is_object($data)) {
            $data = $this->parseOne($data);
        }

        if (is_array($data)) {
            $data = $this->parseMany($data);
        }

        return parent::parse($data);
    }

    /**
     * @param array<object> $documents
     *
     * @return array<object>
     */
    private function parseMany(array $documents): array
    {
        $parsed = [];

        foreach ($documents as $document) {
            $parsed[] = $this->parseOne($document);
        }

        return $parsed;
    }

    private function parseOne(object $document): object
    {
        if (
            property_exists($document, 'type') &&
            property_exists($document, 'meta') &&
            property_exists($document->meta, 'type')
        ) {

            if ($document->type === 'globals') {
                $document->type = 'globals:'.$document->meta->type;
            }

            if ($document->type === 'constructs') {
                $document->type = 'constructs:'.$document->meta->type;
            }
        }

        return $document;
    }
}
