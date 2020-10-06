<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText;

use phpDocumentor\Guides\Environment;
use RuntimeException;
use function preg_replace_callback;
use function sprintf;

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

                $origin = $this->environment->getOrigin();
                if (!$origin->has($path)) {
                    throw new RuntimeException(sprintf('Include "%s" does not exist or is not readable.', $match[0]));
                }

                $contents = $origin->read($path);

                if ($contents === false) {
                    throw new RuntimeException(sprintf('Could not load file from path %s', $path));
                }

                return $this->includeFiles($contents);
            },
            $document
        );
    }
}
