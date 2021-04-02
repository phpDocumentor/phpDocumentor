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

namespace phpDocumentor\FileSystem;

use Flyfinder\Finder;
use InvalidArgumentException;
use League\Flysystem\Adapter\Local;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use LogicException;
use phpDocumentor\Configuration\Source;
use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Dsn;
use Webmozart\Assert\Assert;

use function hash;
use function in_array;
use function sprintf;

use const DIRECTORY_SEPARATOR;
use const LOCK_EX;
use const PHP_OS_FAMILY;

class FlySystemFactory implements FileSystemFactory
{
    /** @var MountManager */
    private $mountManager;

    /** @var Dsn */
    private $outputRoot;

    /** @var array<string, string> */
    private $versionFolders;

    /** @var array<string, array<string, string>>  */
    private $documentationSets;

    public function __construct(MountManager $mountManager)
    {
        $this->mountManager = $mountManager;
    }

    public function setOutputDsn(Dsn $output) : void
    {
        $this->outputRoot = $output;
    }

    public function addVersion(string $versionNumber, string $folder) : void
    {
        $this->versionFolders[$versionNumber] = $folder;
        $this->documentationSets[$versionNumber] = [];
    }

    public function addDocumentationSet(string $versionNumber, Source $source, string $output) : void
    {
        $setId = hash('md5', (string) $source->dsn());

        $this->documentationSets[$versionNumber][$setId] = [
            'source' => $source,
            'output' => $output,
        ];
    }

    public function createDestination(DocumentationSetDescriptor $documentationSetDescriptor) : Filesystem
    {
        $currentId = hash('md5', (string) $documentationSetDescriptor->getSource()->dsn());

        foreach ($this->documentationSets as $versionNumber => $sets) {
            foreach ($sets as $id => $set) {
                if ($id === $currentId) {
                    $path = $this->outputRoot->getPath()->append($this->versionFolders[$versionNumber] . DIRECTORY_SEPARATOR . $set['output']);

                    return $this->create($this->outputRoot->withPath($path));
                }
            }
        }
    }

    /**
     * Returns a Filesystem instance based on the scheme of the provided Dsn
     */
    public function create(Dsn $dsn): Filesystem
    {
        $dsnId = hash('md5', (string) $dsn);

        try {
            $filesystem = $this->mountManager->getFilesystem($dsnId);
        } catch (LogicException $e) {
            $filesystem = new Filesystem($this->createAdapter($dsn));

            $this->mountManager->mountFilesystem($dsnId, $filesystem);
        }

        $filesystem->addPlugin(new Finder());

        Assert::isInstanceOf($filesystem, Filesystem::class);

        return $filesystem;
    }

    private function createAdapter(Dsn $dsn): AdapterInterface
    {
        if (!in_array($dsn->getScheme(), [null, 'file', 'vfs', 'phar'], true)) {
            throw new InvalidArgumentException(sprintf('"%s" is not a supported file system yet', $dsn->getScheme()));
        }

        return new Local(
            $this->formatDsn($dsn),
            $dsn->getScheme() !== 'vfs' ? LOCK_EX : 0, // VFS does not support locking
            Local::SKIP_LINKS
        );
    }

    /**
     * Removes file:/// scheme from the dsn when needed.
     *
     * The local adapter of flysystem cannot handle the file:/// on all windows
     * platforms. At the moment it is unsure why this is exactly happening this way
     * but it seems that php on windows 10 is not able to handle streams properly while
     * windows server is able to do this.
     *
     * Github actions will NOT reproduce this behavior since they are running a server edition of windows.
     */
    private function formatDsn(Dsn $dsn): string
    {
        if (PHP_OS_FAMILY === 'Windows' && $dsn->isWindowsLocalPath() && $dsn->getScheme() === 'file') {
            return (string) $dsn->getPath();
        }

        return (string) $dsn;
    }
}
