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

    /** @var Manifest[] */
    private $manifests;

    /** @var ?ResourceCheckerConfigCache */
    private $cache;

    /** @var string */
    private $extensionsDir;

    private function __construct(string $cacheDir, string $extensionsDir)
    {
        $this->cacheDir = $cacheDir;
        $this->extensionsDir = $extensionsDir;
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
        $this->cache->write('', [new ExtensionsResource($this->manifests)]);
    }

    private function getCache(): ResourceCheckerConfigCache
    {
        if ($this->cache instanceof ResourceCheckerConfigCache) {
            return $this->cache;
        }

        $this->cache = new ResourceCheckerConfigCache(
            $this->cacheDir,
            [new ExtensionLockChecker($this->getManifests())]
        );

        return $this->cache;
    }

    /** @return Manifest[] */
    private function getManifests(): array
    {
        if ($this->manifests !== null) {
            return $this->manifests;
        }

        if (file_exists($this->extensionsDir) === false) {
            $this->manifests = [];

            return $this->manifests;
        }

        $manifests = [];
        $iterator = new DirectoryIterator($this->extensionsDir);
        foreach ($iterator as $dir) {
            if ($dir->isDot()) {
                continue;
            }

            if (!$dir->isDir()) {
                continue;
            }

            $manifests[$dir->getPathName()] = $this->loadManifest(new DirectoryIterator($dir->getPathName()));
        }

        $this->manifests = array_filter($manifests);

        return $this->manifests;
    }

    /** @return Generator<class-string> */
    public function loadExtensions(): Generator
    {
        foreach ($this->getManifests() as $path => $manifest) {
            foreach ($manifest->getBundledComponents() as $component) {
                $namespace = trim($component->getName(), '\\') . '\\';
                AutoloaderLocator::loader()->addPsr4($namespace, [$path]);

                yield $namespace . 'Extension';
            }
        }
    }

    private function loadManifest(DirectoryIterator $dir): ?Manifest
    {
        foreach ($dir as $file) {
            if ($file->isDot()) {
                continue;
            }

            if ($file->isFile() === false) {
                continue;
            }

            if ($file->getFileName() !== 'manifest.xml') {
                continue;
            }

            return ManifestLoader::fromFile($file->getPathName());
        }

        return null;
    }

    public function onBoot(ConsoleCommandEvent $event): void
    {
        $output = $event->getOutput();
        $manifests = $this->getManifests();
        if (count($manifests) > 0) {
            $output->writeln('loaded extensions:');
            foreach ($manifests as $manifest) {
                $output->writeln(
                    $manifest->getName()->asString() . ':' . $manifest->getVersion()->getVersionString(),
                    OutputInterface::OUTPUT_NORMAL | OutputInterface::VERBOSITY_NORMAL
                );
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
