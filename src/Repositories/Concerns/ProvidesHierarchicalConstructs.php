<?php

declare(strict_types=1);

namespace TypedCMS\PHPStarterKit\Repositories\Concerns;

use Illuminate\Support\Collection;
use Swis\JsonApi\Client\Interfaces\DocumentInterface;
use TypedCMS\PHPStarterKit\Models\Construct;

use function collect;

trait ProvidesHierarchicalConstructs
{
    /**
     * @param array<string, mixed> $parameters
     */
    abstract public function all(array $parameters = []): DocumentInterface;

    /**
     * @param array<string, mixed> $parameters
     *
     * @return Collection<int, object>
     */
    public function hierarchy(string $identifier, array $parameters = []): Collection
    {
        $document = $this->all(['hierarchy' => $identifier] + $parameters);

        /** @var Collection<int, Construct> $constructs */
        $constructs = $document->getData();

        return $this->traverseHierarchy($document->getMeta()['hierarchy']->tree, $constructs);
    }

    /**
     * @param array<mixed> $tree
     * @param Collection<int, Construct> $constructs
     *
     * @return Collection<int, object>
     */
    protected function traverseHierarchy(array $tree, Collection $constructs): Collection
    {
        return collect($tree)->map(fn (object $item) => (object) [
            'construct' => $constructs->firstWhere('identifier', $item->construct),
            'children' => $this->traverseHierarchy($item->children, $constructs),
        ]);
    }
}
