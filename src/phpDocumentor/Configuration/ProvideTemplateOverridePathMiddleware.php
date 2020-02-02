<?php

declare(strict_types=1);

namespace phpDocumentor\Configuration;

use League\Uri\Uri;
use phpDocumentor\Dsn;
use phpDocumentor\Path;
use phpDocumentor\Transformer\Writer\Twig\EnvironmentFactory;
use function file_exists;

final class ProvideTemplateOverridePathMiddleware
{
    /** @var EnvironmentFactory */
    private $environmentFactory;

    public function __construct(EnvironmentFactory $environmentFactory)
    {
        $this->environmentFactory = $environmentFactory;
    }

    /**
     * @param array<string, array<string, array<mixed>>> $configuration
     *
     * @return array<string, array<string, array<mixed>>>
     */
    public function __invoke(array $configuration, ?Uri $uri) : array
    {
        $path = $this->normalizePath($uri, new Path('.phpdoc/template'));
        if (file_exists((string) $path)) {
            $this->environmentFactory->withTemplateOverridesAt($path);
        }

        return $configuration;
    }

    public function normalizePath(?Uri $uri, Path $path) : Path
    {
        if (!$uri instanceof Uri) {
            return $path;
        }

        $configFile = Dsn::createFromUri($uri);
        $configPath = $configFile->withPath(Path::dirname($configFile->getPath()));

        return Dsn::createFromString((string) $path)->resolve($configPath)->getPath();
    }
}
