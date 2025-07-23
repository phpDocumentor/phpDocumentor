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
use Jean85\PrettyVersions;
use PharIo\Manifest\ApplicationName;
use phpDocumentor\AutoloaderLocator;
use phpDocumentor\Version;
use SplFileInfo;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Style\SymfonyStyle;

use function array_filter;
use function array_merge;
use function count;
use function file_exists;

final class ExtensionHandler
{
    private static ExtensionHandler|null $instance;

    /** @var ExtensionInfo[] */
    private array|null $extensions = null;

    /** @var ExtensionLoader[] */
    private array $loaders = [];

    /** @var ExtensionInfo[] */
    private array $invalidExtensions =  [];

    /** @var Validator */
    private $validator;

    /** @param string[] $extensionsDirs */
    private function __construct(private array $extensionsDirs = [])
    {
        $this->loaders[] = new PackageLoader();
        $this->loaders[] = new DirectoryLoader();
        $this->loaders[] = new PharLoader();
        $this->validator = new Validator(
            new ApplicationName(PrettyVersions::getRootPackageName()),
            new Version(),
        );
    }

    /** @param string[] $extensionsDirs */
    public static function getInstance(array $extensionsDirs = []): self
    {
        if (isset(self::$instance) === false) {
            self::$instance = new self($extensionsDirs);
        }

        return self::$instance;
    }

    /** @return ExtensionInfo[] */
    private function getExtensions(): array
    {
        if ($this->extensions !== null) {
            return $this->extensions;
        }

        $extensions = [];
        foreach ($this->extensionsDirs as $extensionsDir) {
            $extensions = array_merge($this->collectExtensionsFromDir($extensionsDir), $extensions);
        }

        $this->extensions = array_filter($extensions, function (ExtensionInfo $extension) {
            return $this->validator->isValid($extension);
        });

        $this->invalidExtensions = array_filter($extensions, function (ExtensionInfo $extension) {
            return $this->validator->isValid($extension) === false;
        });

        return $this->extensions;
    }

    /** @return Generator<class-string> */
    public function loadExtensions(): Generator
    {
        foreach ($this->getExtensions() as $extension) {
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
            }
        }

        if (count($this->invalidExtensions) <= 0) {
            return;
        }

        $io->writeln('Failed to load extensions:');
        foreach ($this->invalidExtensions as $extension) {
            $io->warning($extension->getName() . ':' . $extension->getVersion());
        }
    }

    /** @return ExtensionInfo[] */
    private function collectExtensionsFromDir(string $extensionsDir): array
    {
        if (file_exists($extensionsDir) === false) {
            return [];
        }

        $iterator = new DirectoryIterator($extensionsDir);
        $extensions = $this->findExtensionsInDir(new SplFileInfo($extensionsDir));
        foreach ($iterator as $dir) {
            if ($dir->isDot()) {
                continue;
            }

            $extensions = array_merge($extensions, $this->findExtensionsInDir($dir));
        }

        return array_filter($extensions);
    }

    /** @return ExtensionInfo[] */
    private function findExtensionsInDir(SplFileInfo $dir): array
    {
        $extensions = [];

        foreach ($this->loaders as $loader) {
            if (! $loader->supports($dir)) {
                continue;
            }

            $extensions[$dir->getPathName()] = $loader->load($dir);
        }

        return array_filter($extensions);
    }
}
