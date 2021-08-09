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

namespace phpDocumentor\Parser;

use phpDocumentor\Reflection\File;
use Symfony\Component\String\UnicodeString;

use function md5;

final class ReEncodedFile implements File
{
    /** @var string */
    private $path;

    /** @var UnicodeString */
    private $contents;

    public function __construct(string $path, UnicodeString $contents)
    {
        $this->path = $path;
        $this->contents = $contents;
    }

    /**
     * Returns the content of the file as a string.
     */
    public function getContents(): string
    {
        return $this->contents->toString();
    }

    /**
     * Returns md5 hash of the file.
     */
    public function md5(): string
    {
        return md5($this->getContents());
    }

    /**
     * Returns an relative path to the file.
     */
    public function path(): string
    {
        return $this->path;
    }
}
