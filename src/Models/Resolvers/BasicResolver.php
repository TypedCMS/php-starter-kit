<?php

declare(strict_types=1);

namespace TypedCMS\PHPStarterKit\Models\Resolvers;

use DirectoryIterator;
use RegexIterator;
use SplFileInfo;
use Swis\JsonApi\Client\Interfaces\ItemInterface;
use TypedCMS\PHPStarterKit\Models\Construct;
use TypedCMS\PHPStarterKit\Models\Resolvers\Contracts\ResolvesModels;
use TypedCMS\PHPStarterKit\StarterKit;

use function compact;
use function file_exists;
use function str_replace;
use function str_starts_with;

class BasicResolver implements ResolvesModels
{
    public function resolve(string $type): ?ItemInterface
    {
        if ($type === 'constructs' || $type === 'globals') {
            return new Construct();
        }

        if (str_starts_with($type, 'constructs:')) {
            return $this->resolveByConstructsPath(str_replace('constructs:', '', $type));
        }

        if (str_starts_with($type, 'globals:')) {
            return $this->resolveByConstructsPath(str_replace('globals:', '', $type), true);
        }

        return $this->resolveByType($type);
    }

    public function resolveByConstructsPath(string $blueprint, bool $global = false): Construct
    {

        foreach ($this->getModels($global) as $model) {

            if (
                $model instanceof Construct &&
                $model->getBlueprint() === $blueprint
            ) {
                return $model;
            }
        }

        return new Construct([], $global);
    }

    public function resolveByType(string $type): ?ItemInterface
    {
        foreach ($this->getModels() as $model) {

            if ($model->getType() === $type) {
                return $model;
            }
        }

        return null;
    }

    /**
     * @return array<ItemInterface>
     */
    protected function getModels(bool $global = false): array
    {
        $models = [];
        $files = [];

        if (file_exists($this->getPath())) {
            $files = new RegexIterator(new DirectoryIterator($this->getPath()), '/\.php$/');
        }

        /** @var SplFileInfo $file */
        foreach ($files as $file) {

            /** @var object $model */
            $model = StarterKit::container(
                $this->getNamespace() . '\\' . $file->getBasename('.php'),
                compact('global'),
            );

            if (!$model instanceof ItemInterface) {
                continue;
            }

            $models[] = $model;
        }

        return $models;
    }

    protected function getPath(): string
    {
        return StarterKit::config('models_path');
    }

    protected function getNamespace(): string
    {
        return StarterKit::config('models_namespace');
    }
}
