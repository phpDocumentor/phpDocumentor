<?php

declare(strict_types=1);

namespace phpDocumentor\Guides;

use Flyfinder\Path;
use Flyfinder\Specification\AndSpecification;
use Flyfinder\Specification\HasExtension;
use Flyfinder\Specification\InPath;
use InvalidArgumentException;
use League\Flysystem\FilesystemInterface;

use function sprintf;
use function strlen;
use function substr;
use function trim;

class FileCollector
{
    /** @var Metas */
    private $metas;

    /** @var string[][] */
    private $fileInfos = [];

    public function __construct(Metas $metas)
    {
        $this->metas = $metas;
    }

    /**
     * Scans a directory recursively looking for all files to parse.
     *
     * This takes into account the presence of cached & fresh MetaEntry
     * objects, and avoids adding files to the parse queue that have
     * not changed and whose direct dependencies have not changed.
     */
    public function getFiles(FilesystemInterface $filesystem, string $directory, string $extension): Files
    {
        $directory = trim($directory, '/');
        /** @var array<array<string>> $files */
        $files = $filesystem->find(
            new AndSpecification(new InPath(new Path($directory)), new HasExtension([$extension]))
        );

        // completely populate the splFileInfos property
        $this->fileInfos = [];
        foreach ($files as $fileInfo) {
            // Make paths relative to the provided source folder
            $fileInfo['path'] = substr($fileInfo['path'], strlen($directory) + 1);
            $fileInfo['dirname'] = substr($fileInfo['dirname'], strlen($directory) + 1) ?: '';

            $documentPath = $this->getFilenameFromFile($fileInfo);

            $this->fileInfos[$documentPath] = $fileInfo;
        }

        $parseQueue = new Files();
        foreach ($this->fileInfos as $filename => $_fileInfo) {
            if (!$this->doesFileRequireParsing($filename)) {
                continue;
            }

            $parseQueue->add($filename);
        }

        return $parseQueue;
    }

    private function doesFileRequireParsing(string $filename): bool
    {
        if (!isset($this->fileInfos[$filename])) {
            throw new InvalidArgumentException(
                sprintf('No file info found for "%s" - file does not exist.', $filename)
            );
        }

        $file = $this->fileInfos[$filename];

        $documentFilename = $this->getFilenameFromFile($file);
        $entry = $this->metas->get($documentFilename);

        if ($this->hasFileBeenUpdated($filename)) {
            // File is new or changed and thus need to be parsed
            return true;
        }

        // Look to the file's dependencies to know if you need to parse it or not
        $dependencies = $entry !== null ? $entry->getDepends() : [];

        if ($entry !== null && $entry->getParent() !== null) {
            $dependencies[] = $entry->getParent();
        }

        foreach ($dependencies as $dependency) {
            /*
             * The dependency check is NOT recursive on purpose.
             * If fileA has a link to fileB that uses its "headline",
             * for example, then fileA is "dependent" on fileB. If
             * fileB changes, it means that its MetaEntry needs to
             * be updated. And because fileA gets the headline from
             * the MetaEntry, it means that fileA must also be re-parsed.
             * However, if fileB depends on fileC and file C only is
             * updated, fileB *does* need to be re-parsed, but fileA
             * does not, because the MetaEntry for fileB IS still
             * "fresh" - fileB did not actually change, so any metadata
             * about headlines, etc, is still fresh. Therefore, fileA
             * does not need to be parsed.
             */

            // dependency no longer exists? We should re-parse this file
            if (!isset($this->fileInfos[$dependency])) {
                return true;
            }

            // finally, we need to recursively ask if this file needs parsing
            if ($this->hasFileBeenUpdated($dependency)) {
                return true;
            }
        }

        // Meta is fresh and no dependencies need parsing
        return false;
    }

    private function hasFileBeenUpdated(string $filename): bool
    {
        /** @var array<string> $file */
        $file = $this->fileInfos[$filename];

        $documentFilename = $this->getFilenameFromFile($file);

        /** @var array<string>|null $entry */
        $entry = $this->metas->get($documentFilename);

        // File is new or changed
        return $entry === null || $entry['timestamp'] < $file['timestamp'];
    }

    /**
     * Converts foo/bar.rst to foo/bar (the document filename)
     *
     * @param array<string> $fileInfo
     */
    private function getFilenameFromFile(array $fileInfo): string
    {
        $directory = $fileInfo['dirname'] ? $fileInfo['dirname'] . '/' : '';

        return $directory . $fileInfo['filename'];
    }
}
