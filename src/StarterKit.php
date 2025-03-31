<?php

declare(strict_types=1);

namespace TypedCMS\PHPStarterKit;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use Illuminate\Container\Container;
use kamermans\OAuth2\GrantType\AuthorizationCode;
use kamermans\OAuth2\GrantType\RefreshToken;
use kamermans\OAuth2\OAuth2Middleware;
use kamermans\OAuth2\Persistence\FileTokenPersistence;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Swis\JsonApi\Client\Client;
use Swis\JsonApi\Client\DocumentClient;
use Swis\JsonApi\Client\Interfaces\ClientInterface;
use Swis\JsonApi\Client\Interfaces\DocumentClientInterface;
use Swis\JsonApi\Client\Interfaces\DocumentParserInterface;
use Swis\JsonApi\Client\Interfaces\ResponseParserInterface;
use Swis\JsonApi\Client\Interfaces\TypeMapperInterface;
use Swis\JsonApi\Client\Parsers\ResponseParser;
use TypedCMS\PHPStarterKit\Models\Resolvers\BasicResolver as ModelsBasicResolver;
use TypedCMS\PHPStarterKit\Models\Resolvers\Contracts\ResolvesModels;
use TypedCMS\PHPStarterKit\Repositories\GlobalsRepository;
use TypedCMS\PHPStarterKit\Repositories\Resolvers\BasicResolver as RepositoriesBasicResolver;
use TypedCMS\PHPStarterKit\Repositories\Resolvers\Contracts\ResolvesRepositories;

use function array_flip;
use function array_intersect_key;
use function array_merge;
use function is_string;

class StarterKit
{
    /**
     * @var array<string, mixed>
     */
    private static array $config = [
        'base_uri' => '@team/project',
        'client_id' => null,
        'client_secret' => null,
        'redirect_uri' => null,
        'code' => null,
        'scope' => 'delivery,access-user-data',
        'token_path' => '/path/to/my/token/file.txt',
        'globals_repository' => GlobalsRepository::class,
        'models_path' => '/path/to/my/models',
        'models_namespace' => 'App\\Models',
        'models_resolver' => ModelsBasicResolver::class,
        'repositories_path' => '/path/to/my/repositories',
        'repositories_namespace' => 'App\\Repositories',
        'repositories_resolver' => RepositoriesBasicResolver::class,
    ];

    /**
     * @param array<string, mixed> $config
     */
    final public static function configure(array $config): void
    {
        self::$config = array_merge(self::$config, $config);

        self::register();
    }

    /**
     * @param array<string>|string|null $key
     */
    final public static function config(array|string|null $key = null): mixed
    {
        if ($key === null) {
            return self::$config;
        }

        if (is_string($key)) {
            return self::$config[$key];
        }

        return array_intersect_key(self::$config, array_flip($key));
    }

    /**
     * @param array<string, mixed> $parameters
     */
    final public static function container(
        string|callable|null $abstract = null,
        array $parameters = [],
    ): mixed {

        $container = Container::getInstance();

        if ($abstract === null) {
            return $container;
        }

        return $container->make($abstract, $parameters);
    }

    protected static function register(): void
    {
        static::bindHttpClient();
        static::registerSwisJsonApi();
        static::registerResolvers();
    }

    protected static function bindHttpClient(): void
    {
        static::container()->bind(HttpClientInterface::class, function (): GuzzleClient {

            $authClient = new GuzzleClient(['base_uri' => 'https://app.typedcms.com/oauth/token']);

            $authConfig = static::config([
                'client_id',
                'client_secret',
                'redirect_uri',
                'code',
                'scope',
            ]);

            $oauth = new OAuth2Middleware(
                new AuthorizationCode($authClient, $authConfig),
                new RefreshToken($authClient, $authConfig)
            );

            $storage = new FileTokenPersistence(static::config('token_path'));

            $oauth->setTokenPersistence($storage);

            $stack = HandlerStack::create();
            $stack->push($oauth);

            return new GuzzleClient(['auth' => 'oauth', 'handler' => $stack]);
        });
    }

    protected static function registerSwisJsonApi(): void
    {
        static::container()->bind(TypeMapperInterface::class, TypeMapper::class);
        static::container()->singleton(TypeMapper::class);

        static::container()->bind(DocumentParserInterface::class, DocumentParser::class);
        static::container()->bind(ResponseParserInterface::class, ResponseParser::class);

        static::container()->bind(ClientInterface::class, Client::class);
        static::container()->bind(DocumentClientInterface::class, DocumentClient::class);
    }

    protected static function registerResolvers(): void
    {
        static::container()->singleton(
            ResolvesModels::class,
            fn () => static::container(static::config('models_resolver')),
        );
        static::container()->singleton(
            ResolvesRepositories::class,
            fn () => static::container(static::config('repositories_resolver')),
        );
    }
}
