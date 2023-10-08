<?php

declare(strict_types=1);

namespace TypedCMS\PHPStarterKit\Repositories;

use RuntimeException;
use Swis\JsonApi\Client\Error;
use Swis\JsonApi\Client\Interfaces\DocumentInterface;
use Swis\JsonApi\Client\Interfaces\ItemInterface;
use Swis\JsonApi\Client\InvalidResponseDocument;
use Swis\JsonApi\Client\Repository as BaseRepository;
use TypedCMS\PHPStarterKit\Repositories\Concerns\DeterminesEndpoint;
use TypedCMS\PHPStarterKit\StarterKit;

abstract class Repository extends BaseRepository
{
    use DeterminesEndpoint;

    /**
     * By default, repositories make requests to the delivery api. Set this to
     * true if you wish to use the management api by default.
     */
    protected bool $mapi = false;

    static public function make(): static
    {
        return StarterKit::container(static::class);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function all(array $parameters = []): DocumentInterface
    {
        $parameters += ['all' => true];

        return $this->handleErrors(parent::all($parameters), strict: true);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function take(array $parameters = [])
    {
        return $this->handleErrors(parent::take($parameters), strict: true);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function find(string $id, array $parameters = []): DocumentInterface
    {
        return $this->handleErrors(parent::find($id, $parameters));
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function findOrFail(string $id, array $parameters = []): DocumentInterface
    {
        return $this->handleErrors(parent::find($id, $parameters), fail: true);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function save(ItemInterface $item, array $parameters = []): DocumentInterface
    {
        $this->mapi();

        return parent::save($item, $parameters);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function delete(string $id, array $parameters = []): DocumentInterface
    {
        $this->mapi();

        return parent::delete($id, $parameters);
    }

    protected function handleErrors(DocumentInterface $document, bool $fail = false, bool $strict = false): DocumentInterface
    {
        if ($document instanceof InvalidResponseDocument || $document->hasErrors()) {

            if (!$strict && $document->getResponse()->getStatusCode() === 404) {

                if ($fail) {
                    $this->handle404Error($document);
                }

                return $document;
            }

            foreach ($document->getErrors() as $error) {
                $this->logError($error);
            }

            throw new RuntimeException('Errors occurred whilst fetching data from the API.');
        }

        return $document;
    }

    protected function handle404Error(DocumentInterface $document): void
    {

    }

    protected function logError(Error $error): void
    {

    }
}

