<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Builder;

use InvalidArgumentException;
use function array_filter;
use function array_key_exists;
use function array_keys;
use function sprintf;

final class ParseQueue
{
    /**
     * An array where each key is the filename and the value is a
     * boolean indicating if the file needs to be parsed or not.
     *
     * @var bool[]
     */
    private $fileStatuses = [];

    public function addFile(string $filename, bool $parseNeeded) : void
    {
        if (isset($this->fileStatuses[$filename])) {
            throw new InvalidArgumentException(sprintf('File "%s" is already in the parse queue', $filename));
        }

        $this->fileStatuses[$filename] = $parseNeeded;
    }

    public function isFileKnownToParseQueue(string $filename) : bool
    {
        return array_key_exists($filename, $this->fileStatuses);
    }

    public function doesFileRequireParsing(string $filename) : bool
    {
        if (! $this->isFileKnownToParseQueue($filename)) {
            throw new InvalidArgumentException(sprintf('File "%s" is not known to the parse queue', $filename));
        }

        return $this->fileStatuses[$filename];
    }

    /**
     * @return string[]
     */
    public function getAllFilesThatRequireParsing() : array
    {
        return array_keys(array_filter($this->fileStatuses, static function (bool $parseNeeded) {
            return $parseNeeded;
        }));
    }
}
