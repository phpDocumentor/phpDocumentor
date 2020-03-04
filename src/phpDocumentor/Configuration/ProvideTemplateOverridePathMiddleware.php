<?php

declare(strict_types=1);

namespace phpDocumentor\Configuration;

use League\Uri\Contracts\UriInterface;
use phpDocumentor\Dsn;
use phpDocumentor\Path;
use phpDocumentor\Transformer\Writer\Twig\EnvironmentFactory;
use function file_exists;

final class ProvideTemplateOverridePathMiddleware implements MiddlewareInterface
{
    /** @var EnvironmentFactory */
    private $environmentFactory;

    public function __construct(EnvironmentFactory $environmentFactory)
    {
        $this->environmentFactory = $environmentFactory;
    }

    /**
     * @param array<string, array<string, array<string, mixed>>> $configuration
     *
     * @return array<string, array<string, array<string, mixed>>>
     */
    public function __invoke(array $configuration, ?UriInterface $uri) : array
    {
        $path = $this->normalizePath($uri, new Path('.phpdoc/template'));
        if (file_exists((string) $path)) {
            $this->environmentFactory->withTemplateOverridesAt($path);
        }

        return $configuration;
    }

    public function normalizePath(?UriInterface $uri, Path $path) : Path
    {
        if ($uri === null) {
            return $path;
        }

        $configFile = Dsn::createFromUri($uri);
        $configPath = $configFile->withPath(Path::dirname($configFile->getPath()));

        return Dsn::createFromString((string) $path)->resolve($configPath)->getPath();
    }
}
