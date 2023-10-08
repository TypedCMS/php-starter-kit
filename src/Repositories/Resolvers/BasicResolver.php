<?php

declare(strict_types=1);

namespace TypedCMS\PHPStarterKit\Repositories\Resolvers;

use DirectoryIterator;
use RegexIterator;
use SplFileInfo;
use TypedCMS\PHPStarterKit\Repositories\Contracts\CollectsConstructs;
use TypedCMS\PHPStarterKit\Repositories\Repository;
use TypedCMS\PHPStarterKit\Repositories\Resolvers\Contracts\ResolvesRepositories;
use TypedCMS\PHPStarterKit\StarterKit;
use UnexpectedValueException;

use function file_exists;

class BasicResolver implements ResolvesRepositories
{
    /**
     * @var array<Repository>|null
     */
    protected ?array $repos = null;

    public function resolveByBlueprint(string $blueprint): array
    {
        $repos = [];

        foreach ($this->getRepositories() as $repo) {

            if (
                $repo instanceof CollectsConstructs &&
                $repo->getBlueprint() === $blueprint
            ) {
                $repos[] = $repo;
            }
        }

        return $repos;
    }

    public function resolveByEndpoint(string $endpoint): array
    {
        $repos = [];

        foreach ($this->getRepositories() as $repo) {

            if ($repo->getSpecifiedEndpoint() === $endpoint) {
                $repos[] = $repo;
            }
        }

        return $repos;
    }

    /**
     * @return array<Repository>
     */
    protected function getRepositories(): array
    {
        if ($this->repos === null) {

            $this->repos = [];
            $files = [];

            if (file_exists($this->getPath())) {
                $files = new RegexIterator(new DirectoryIterator($this->getPath()), '/\.php$/');
            }

            /** @var SplFileInfo $file */
            foreach ($files as $file) {

                /** @var object $repo */
                $repo = StarterKit::container($this->getNamespace() . '\\' . $file->getBasename('.php'));

                if (!$repo instanceof Repository) {
                    throw new UnexpectedValueException('Resolved repositories must be instances of ' . Repository::class);
                }

                $this->repos[] = $repo;
            }
        }

        return $this->repos;
    }

    protected function getPath(): string
    {
        return StarterKit::config('repositories_path');
    }

    protected function getNamespace(): string
    {
        return StarterKit::config('repositories_namespace');
    }
}
