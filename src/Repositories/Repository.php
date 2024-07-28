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

use function array_filter;
use function array_unique;
use function count;
use function explode;
use function implode;

abstract class Repository extends BaseRepository
{
    use DeterminesEndpoint;

    /**
     * By default, repositories make requests to the delivery api. Set this to
     * true if you wish to use the management api by default.
     */
    protected bool $mapi = false;

    /**
     * @var array<string>
     */
    protected array $with = [];

    static public function make(): static
    {
        return StarterKit::container(static::class);
    }

    /**
     * @param array<string, mixed> $parameters
     * @param array<string, mixed> $headers
     */
    public function all(array $parameters = [], array $headers = []): DocumentInterface
    {
        $parameters += ['all' => true];

        return $this->handleErrors(parent::all($this->getParameters($parameters), $headers), strict: true);
    }

    /**
     * @param array<string, mixed> $parameters
     * @param array<string, mixed> $headers
     */
    public function take(array $parameters = [], array $headers = []): DocumentInterface
    {
        return $this->handleErrors(parent::take($this->getParameters($parameters), $headers), strict: true);
    }

    /**
     * @param array<string, mixed> $parameters
     * @param array<string, mixed> $headers
     */
    public function find(string $id, array $parameters = [], array $headers = []): DocumentInterface
    {
        return $this->handleErrors(parent::find($id, $this->getParameters($parameters), $headers));
    }

    /**
     * @param array<string, mixed> $parameters
     * @param array<string, mixed> $headers
     */
    public function findOrFail(string $id, array $parameters = [], array $headers = []): DocumentInterface
    {
        return $this->handleErrors(parent::find($id, $this->getParameters($parameters), $headers), fail: true);
    }

    /**
     * @param array<string, mixed> $parameters
     * @param array<string, mixed> $headers
     */
    public function save(ItemInterface $item, array $parameters = [], array $headers = []): DocumentInterface
    {
        $this->mapi();

        return parent::save($item, $this->getParameters($parameters), $headers);
    }

    /**
     * @param array<string, mixed> $parameters
     * @param array<string, mixed> $headers
     */
    public function delete(string $id, array $parameters = [], array $headers = []): DocumentInterface
    {
        $this->mapi();

        return parent::delete($id, $this->getParameters($parameters), $headers);
    }

    protected function handleErrors(
        DocumentInterface $document,
        bool $fail = false,
        bool $strict = false,
    ): DocumentInterface {

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

    /**
     * @param array<string, mixed> $parameters
     *
     * @return array<string, mixed>
     */
    protected function getParameters(array $parameters): array
    {
        if (count($this->with) !== 0) {
            $parameters['include'] = implode(',', array_filter(array_unique([
                ...$this->with,
                ...explode(',', $parameters['include'] ?? ''),
            ])));
        }

        return $parameters;
    }

    protected function handle404Error(DocumentInterface $document): void
    {

    }

    protected function logError(Error $error): void
    {

    }
}

