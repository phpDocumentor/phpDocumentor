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

namespace phpDocumentor\Extension;

use DirectoryIterator;
use Generator;
use PharIo\Manifest\Manifest;
use PharIo\Manifest\ManifestLoader;
use phpDocumentor\AutoloaderLocator;
use Symfony\Component\Config\ResourceCheckerConfigCache;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use function array_filter;
use function file_exists;
use function trim;

final class ExtensionHandler implements EventSubscriberInterface
{
    /**
     * @var ExtensionHandler
     */
    private static $instance;

    /** @var string */
    private $cacheDir;

    /** @var Extension[] */
    private $extensions;

    /** @var ?ResourceCheckerConfigCache */
    private $cache;

    /** @var string */
    private $extensionsDir;

    /** @var ExtensionLoader[] */
    private $loaders = [];

    private function __construct(string $cacheDir, string $extensionsDir)
    {
        $this->cacheDir = $cacheDir;
        $this->extensionsDir = $extensionsDir;
        $this->loaders[] = new DirectoryLoader();
    }

    public static function getInstance(string $cacheDir, string $extensionsDir): self
    {
        if (self::$instance instanceof self === false) {
            self::$instance = new self($cacheDir, $extensionsDir);
        }

        return self::$instance;
    }

    public function isFresh(): bool
    {
        $extensionConfig = $this->getCache();
        $fresh = $extensionConfig->isFresh();
        if ($fresh === false) {
            $this->refresh();
        }

        return $fresh;
    }

    public function refresh(): void
    {
        $this->cache->write('', [new ExtensionsResource($this->extensions)]);
    }

    private function getCache(): ResourceCheckerConfigCache
    {
        if ($this->cache instanceof ResourceCheckerConfigCache) {
            return $this->cache;
        }

        $this->cache = new ResourceCheckerConfigCache(
            $this->cacheDir,
            [new ExtensionLockChecker($this->getExtensions())]
        );

        return $this->cache;
    }

    /** @return Extension[] */
    private function getExtensions(): array
    {
        if ($this->extensions !== null) {
            return $this->extensions;
        }

        if (file_exists($this->extensionsDir) === false) {
            $this->extensions = [];

            return $this->extensions;
        }

        $extensions = [];
        $iterator = new DirectoryIterator($this->extensionsDir);
        foreach ($iterator as $dir) {
            if ($dir->isDot()) {
                continue;
            }

            foreach ($this->loaders as $loader) {
                if ($loader->supports($dir)) {
                    $extensions[$dir->getPathName()] = $loader->load(new DirectoryIterator($dir->getPathName()));
                }
            }
        }

        $this->extensions = array_filter($extensions);

        return $this->extensions;
    }

    /** @return Generator<class-string> */
    public function loadExtensions(): Generator
    {
        foreach ($this->getExtensions() as $path => $extension) {
                $namespace = $extension->getNamespace();
                AutoloaderLocator::loader()->addPsr4($namespace, [$extension->getPath()]);

                yield $extension->getExtensionClass();
        }
    }

    public function onBoot(ConsoleCommandEvent $event): void
    {
        $io = new SymfonyStyle($event->getInput(), $event->getOutput());
        $extensions = $this->getExtensions();
        if (count($extensions) > 0) {
            $io->writeln('Loaded extensions:');
            foreach ($extensions as $extension) {
                $io->success($extension->getName() . ':' . $extension->getVersion());
                $io->warning($extension->getName() . ':' . $extension->getVersion());
            }
        }
    }

    /** @return string[] */
    public static function getSubscribedEvents(): array
    {
        return [
            'console.command' => 'onBoot'
        ];
    }
}
