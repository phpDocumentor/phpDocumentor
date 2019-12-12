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

namespace phpDocumentor\Application\Stage;

use InvalidArgumentException;
use phpDocumentor\Configuration\CommandlineOptionsMiddleware;
use phpDocumentor\Configuration\Configuration;
use phpDocumentor\Configuration\ConfigurationFactory;
use phpDocumentor\Uri;
use Psr\Log\LoggerInterface;
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

    public function __construct(
        ConfigurationFactory $configFactory,
        Configuration $configuration,
        LoggerInterface $logger
    ) {
        $this->configFactory = $configFactory;
        $this->configuration = $configuration;
        $this->logger = $logger;
    }

    /**
     * @return string[]
     */
    public function __invoke(array $options) : array
    {
        $this->configFactory->addMiddleware(
            new CommandlineOptionsMiddleware($options)
        );

        if ($options['config'] ?? null) {
            $path = $options['config'];
            // if the path equals none then we fallback to the defaults but
            // don't load anything from the filesystem
            if ($path !== 'none') {
                $uri = realpath($path);
                if ($uri === false) {
                    throw new InvalidArgumentException(
                        sprintf(
                            'The configuration file in path "%s" can not be '
                            . 'found or read',
                            $path
                        )
                    );
                }

                $this->logger->notice(sprintf('Using the configuration file at: %s', $path));
                $this->configuration->exchangeArray(
                    $this->configFactory->fromUri(new Uri($uri))->getArrayCopy()
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

        return ['phpdocumentor' => $this->configuration->getArrayCopy()];
    }
}
