<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Builder;

use phpDocumentor\Guides\RestructuredText\Meta\Metas;
use InvalidArgumentException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use function sprintf;
use function strlen;
use function substr;

class Scanner
{
    /** @var string */
    private $fileExtension;

    /** @var string */
    private $directory;

    /** @var Metas */
    private $metas;

    /** @var Finder */
    private $finder;

    /** @var SplFileInfo[] */
    private $fileInfos = [];

    public function __construct(string $fileExtension, string $directory, Metas $metas, ?Finder $finder = null)
    {
        $this->fileExtension = $fileExtension;
        $this->directory     = $directory;
        $this->metas         = $metas;

        $this->finder = $finder ?: new Finder();
        $this->finder->in($this->directory)
            ->files()
            ->name('*.' . $this->fileExtension);
    }

    /**
     * Scans a directory recursively looking for all files to parse.
     *
     * This takes into account the presence of cached & fresh MetaEntry
     * objects, and avoids adding files to the parse queue that have
     * not changed and whose direct dependencies have not changed.
     */
    public function scan() : ParseQueue
    {
        // completely populate the splFileInfos property
        $this->fileInfos = [];
        foreach ($this->finder as $fileInfo) {
            $relativeFilename = $fileInfo->getRelativePathname();
            // strip off the extension
            $documentPath = substr($relativeFilename, 0, -(strlen($this->fileExtension) + 1));

            $this->fileInfos[$documentPath] = $fileInfo;
        }

        $parseQueue = new ParseQueue();
        foreach ($this->fileInfos as $filename => $fileInfo) {
            $parseQueue->addFile(
                $filename,
                $this->doesFileRequireParsing($filename, $parseQueue)
            );
        }

        return $parseQueue;
    }

    private function doesFileRequireParsing(string $filename, ParseQueue $parseQueue) : bool
    {
        if (! isset($this->fileInfos[$filename])) {
            throw new InvalidArgumentException(sprintf('No file info found for "%s" - file does not exist.', $filename));
        }

        $file = $this->fileInfos[$filename];

        $documentFilename = $this->getFilenameFromFile($file);
        $entry            = $this->metas->get($documentFilename);

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
            if (! isset($this->fileInfos[$dependency])) {
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

    private function hasFileBeenUpdated(string $filename) : bool
    {
        $file = $this->fileInfos[$filename];

        $documentFilename = $this->getFilenameFromFile($file);
        $entry            = $this->metas->get($documentFilename);

        // File is new or changed
        return $entry === null || $entry->getMtime() < $file->getMTime();
    }

    /**
     * Converts foo/bar.rst to foo/bar (the document filename)
     */
    private function getFilenameFromFile(SplFileInfo $file) : string
    {
        return substr($file->getRelativePathname(), 0, -(strlen($this->fileExtension) + 1));
    }
}
