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
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Style\SymfonyStyle;

use function array_filter;
use function count;
use function file_exists;

final class ExtensionHandler
{
    private static ExtensionHandler|null $instance;

    /** @var ExtensionInfo[] */
    private array|null $extensions = null;

    /** @var string */
    private $extensionsDir;

    /** @var ExtensionLoader[] */
    private $loaders = [];

    /** @var ExtensionInfo[] */
    private array $invalidExtensions =  [];

    /** @var Validator */
    private $validator;

    private function __construct(string $extensionsDir)
    {
        $this->extensionsDir = $extensionsDir;
        $this->loaders[] = new DirectoryLoader();
        $this->validator = new Validator(
            new ApplicationName(PrettyVersions::getRootPackageName()),
            new Version(),
        );
    }

    public static function getInstance(string $extensionsDir = ''): self
    {
        if (isset(self::$instance) === false) {
            self::$instance = new self($extensionsDir);
        }

        return self::$instance;
    }

    /** @return ExtensionInfo[] */
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
                if (! $loader->supports($dir)) {
                    continue;
                }

                $extensions[$dir->getPathName()] = $loader->load(new DirectoryIterator($dir->getPathName()));
            }
        }

        $extensions = array_filter($extensions);
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
}
