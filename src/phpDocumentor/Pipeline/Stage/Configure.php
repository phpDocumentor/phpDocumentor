<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Pipeline\Stage;

use InvalidArgumentException;
use phpDocumentor\Configuration\CommandlineOptionsMiddleware;
use phpDocumentor\Configuration\Configuration;
use phpDocumentor\Configuration\ConfigurationFactory;
use phpDocumentor\Configuration\PathNormalizingMiddleware;
use phpDocumentor\Configuration\ProvideTemplateOverridePathMiddleware;
use phpDocumentor\Parser\Cache\Locator;
use phpDocumentor\Transformer\Writer\Twig\EnvironmentFactory;
use phpDocumentor\UriFactory;
use Psr\Log\LoggerInterface;

use function realpath;
use function sprintf;

final class Configure
{
    public function __construct(
        private readonly ConfigurationFactory $configFactory,
        private Configuration $configuration,
        private readonly LoggerInterface $logger,
        private readonly Locator $locator,
        private readonly EnvironmentFactory $environmentFactory,
        private readonly string $currentWorkingDir,
    ) {
    }

    /**
     * @param array<string|string[]> $options
     *
     * @return array<string, array<mixed>>
     */
    public function __invoke(array $options): array
    {
        $this->configFactory->addMiddleware(
            new CommandlineOptionsMiddleware($options, $this->configFactory, $this->currentWorkingDir),
        );
        $this->configFactory->addMiddleware(new PathNormalizingMiddleware());
        $this->configFactory->addMiddleware(new ProvideTemplateOverridePathMiddleware($this->environmentFactory));

        $this->loadConfigurationFile($options['config'] ?? '');
        $this->locator->providePath($this->configuration['phpdocumentor']['paths']['cache']);
        $this->logger->info(sprintf('Logging to: %s', (string) $this->locator->locate()));

        return $this->configuration->getArrayCopy();
    }

    private function loadConfigurationFile(string $path): void
    {
        if ($path === '') {
            $this->logger->notice('Using the configuration file at the default location');
            $this->configuration->exchangeArray($this->configFactory->fromDefaultLocations()->getArrayCopy());

            return;
        }

        // if the path equals none then we fallback to the defaults but don't load anything from the filesystem
        if ($path === 'none') {
            $this->logger->notice('Not using any configuration file, relying on application defaults');
            $this->configuration->exchangeArray(
                $this->configFactory->fromDefault()->getArrayCopy(),
            );

            return;
        }

        $uri = realpath($path);
        if ($uri === false) {
            throw new InvalidArgumentException(
                sprintf('The configuration file in path "%s" can not be found or read', $path),
            );
        }

        $this->logger->notice(sprintf('Using the configuration file at: %s', $path));
        $this->configuration->exchangeArray(
            $this->configFactory->fromUri(UriFactory::createUri($uri))->getArrayCopy(),
        );
    }
}
