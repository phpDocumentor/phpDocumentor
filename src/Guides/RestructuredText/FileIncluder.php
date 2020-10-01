<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText;

use RuntimeException;
use function explode;
use function file_exists;
use function file_get_contents;
use function is_readable;
use function preg_replace_callback;
use function realpath;
use function sprintf;
use function strpos;

class FileIncluder
{
    /** @var Environment */
    private $environment;

    /** @var bool */
    private $includeAllowed = true;

    /** @var string */
    private $includeRoot;

    public function __construct(
        Environment $environment,
        bool $includeAllowed,
        string $includeRoot
    ) {
        $this->environment    = $environment;
        $this->includeAllowed = $includeAllowed;
        $this->includeRoot    = $includeRoot;
    }

    public function includeFiles(string $document) : string
    {
        return preg_replace_callback(
            '/^\.\. include:: (.+)$/m',
            function ($match) {
                $path = $this->environment->absoluteRelativePath($match[1]);

                if (! file_exists($path)) {
                    throw new RuntimeException(sprintf('Include "%s" does not exist or is not readable.', $match[0]));
                }

                if ($this->isFileIncludeAllowed($path)) {
                    $contents = file_get_contents($path);

                    if ($contents === false) {
                        throw new RuntimeException(sprintf('Could not load file from path %s', $path));
                    }

                    return $this->includeFiles($contents);
                }

                return '';
            },
            $document
        );
    }

    private function isFileIncludeAllowed(string $path) : bool
    {
        if (! $this->includeAllowed) {
            return false;
        }

        if (! @is_readable($path)) {
            return false;
        }

        if ($this->includeRoot === '') {
            return true;
        }

        $real = realpath($path);

        if ($real === false) {
            return false;
        }

        foreach (explode(':', $this->includeRoot) as $root) {
            if (strpos($real, $root) === 0) {
                return true;
            }
        }

        return false;
    }
}
