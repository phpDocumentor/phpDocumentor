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

namespace phpDocumentor;

use Composer\Autoload\ClassLoader;
use InvalidArgumentException;
use League\Uri\Uri;
use League\Uri\UriInfo;
use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\ProjectDescriptor;
use Psr\Log\LoggerInterface;
use function getcwd;
use function is_callable;
use function ob_get_clean;
use function ob_start;
use function sprintf;

final class Extensions implements CompilerPassInterface
{
    /** @var integer represents the priority in the Compiler queue. Should be slightly more than the transformer */
    public const COMPILER_PRIORITY = 5500;

    /** @var ClassLoader */
    private $loader;

    /** @var callable[] */
    private $extensions;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger, array $extensions = [], $locations = [])
    {
        $this->loader = AutoloaderLocator::loader();

        $this->logger = $logger;
        foreach ($locations as $namespace => $location) {
            $this->registerLocation($namespace, $location);
        }
        foreach ($extensions as $extension) {
            $this->registerExtension($extension);
        }
    }

    /**
     * Registers a namespace and location from where to load extensions.
     *
     * This is the big 'secret' behind extensions in phpDocumentor. Because we register the location dynamically we can
     * refer to classes outside of the current project structure, and even outside of a PHAR when using the PHAR version
     * of phpDocumentor.
     *
     * phpDocumentor uses the PSR-4 standard for autoloading extensions; this means that the name of the class must
     * match the name of the file and only 1 class per file is allowed.
     *
     * @param string $namespace The namespace that matches the location.
     * @param string $location The path from where to load extensions matching the namespace, when relative it will be
     *     considered to be relative to the current working directory.
     */
    public function registerLocation(string $namespace, string $location) : void
    {
        if (UriInfo::isRelativePath(Uri::createFromString($location))) {
            $location = getcwd() . DIRECTORY_SEPARATOR . $location;
        }

        // Some loaders double the number of slashes; causing autoloading to fail
        if (strpos($namespace, '\\\\')) {
            $namespace = stripslashes($namespace);
        }

        $this->loader->addPsr4(trim($namespace, '\\') . '\\', $location);
    }

    /**
     * Registers the given class as an extension and immediately instantiates it.
     *
     * It is important that the location for this extension needs to have been registered before registering this
     * extension; on moment of registration it will be instantiated and for that we need to be able to resolve it.
     *
     * @param string $className
     * @see self::registerLocation() for more information on how to register locations for extensions.
     */
    public function registerExtension(string $className) : void
    {
        if ($this->loader->loadClass($className) !== true) {
            throw new InvalidArgumentException(
                sprintf(
                    'Could not find extension with class name "%s"; did you register its location?',
                    $className
                )
            );
        }

        $extension = new $className();
        if (!is_callable($extension)) {
            throw new InvalidArgumentException(
                sprintf('Extension %s is not callable, please implement the `__invoke()` method', $className)
            );
        }

        $this->extensions[] = $extension;
    }

    /**
     * @inheritDoc
     */
    public function getDescription() : string
    {
        $extensions = [];
        foreach ($this->extensions as $extension) {
            $extensions[] = $this->extensionName($extension);
        }

        return 'Apply the effect of the extensions: ' . implode(', ', $extensions);
    }

    /**
     * @inheritDoc
     */
    public function execute(ProjectDescriptor $project) : void
    {
        foreach ($this->extensions as $extension) {
            ob_start();
            $extension($project);
            $output = trim(ob_get_clean());
            if ($output) {
                $this->logger->notice(sprintf("    %s: %s", $this->extensionName($extension), $output));
            }
        }
    }

    public function __toString() : string
    {
        return $this->getDescription();
    }

    private function extensionName(callable $extension) : string
    {
        return substr(get_class($extension), strripos(get_class($extension), '\\') + 1) ?: 'Unknown';
    }
}
