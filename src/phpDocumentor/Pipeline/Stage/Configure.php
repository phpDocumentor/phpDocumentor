<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Pipeline\Stage;

use InvalidArgumentException;
use phpDocumentor\Configuration\CommandlineOptionsMiddleware;
use phpDocumentor\Configuration\Configuration;
use phpDocumentor\Configuration\ConfigurationFactory;
use phpDocumentor\Configuration\PathNormalizingMiddleware;
use phpDocumentor\Parser\Cache\Locator;
use phpDocumentor\UriFactory;
use Psr\Log\LoggerInterface;
use function getcwd;
use function realpath;
use function sprintf;

final class Configure
{
    /** @var ConfigurationFactory */
    private $configFactory;

    /** @var Configuration */
    private $configuration;

    /** @var LoggerInterface */
    private $logger;

    /** @var Locator */
    private $locator;

    public function __construct(
        ConfigurationFactory $configFactory,
        Configuration $configuration,
        LoggerInterface $logger,
        Locator $locator
    ) {
        $this->configFactory = $configFactory;
        $this->configuration = $configuration;
        $this->logger = $logger;
        $this->locator = $locator;
    }

    /**
     * @return array<string, array>
     */
    public function __invoke(array $options) : array
    {
        $this->configFactory->addMiddleware(
            new CommandlineOptionsMiddleware($options, $this->configFactory, 'file:///' . getcwd())
        );
        $this->configFactory->addMiddleware(new PathNormalizingMiddleware());

        if ($options['config'] ?? null) {
            $path = $options['config'];
            // if the path equals none then we fallback to the defaults but
            // don't load anything from the filesystem
            if ($path !== 'none') {
                $uri = realpath($path);
                if ($uri === false) {
                    throw new InvalidArgumentException(
                        sprintf(
                            'The configuration file in path "%s" can not be found or read',
                            $path
                        )
                    );
                }

                $this->logger->notice(sprintf('Using the configuration file at: %s', $path));
                $this->configuration->exchangeArray(
                    $this->configFactory->fromUri(UriFactory::createUri($uri))->getArrayCopy()
                );
            } else {
                $this->logger->notice('Not using any configuration file, relying on application defaults');
            }
        } else {
            $this->logger->notice('Using the configuration file at the default location');
            $this->configuration->exchangeArray(
                $this->configFactory->fromDefaultLocations()->getArrayCopy()
            );
        }

        $this->locator->providePath($this->configuration['phpdocumentor']['paths']['cache']);
        return $this->configuration->getArrayCopy();
    }
}
