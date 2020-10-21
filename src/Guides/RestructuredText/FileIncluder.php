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

    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    public function includeFiles(string $document) : string
    {
        return preg_replace_callback(
            '/^\.\. include:: (.+)$/m',
            function ($match) {
                $path = $this->environment->absoluteRelativePath($match[1]);

                $origin = $this->environment->getOrigin();
                if (!$origin->has($path)) {
                    throw new RuntimeException(
                        sprintf('Include "%s" (%s) does not exist or is not readable.', $match[0], $path)
                    );
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
