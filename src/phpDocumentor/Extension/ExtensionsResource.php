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

use PharIo\Manifest\Manifest;
use Symfony\Component\Config\Resource\ResourceInterface;

use function md5;
use function serialize;

final class ExtensionsResource implements ResourceInterface
{
    /** @var Extension[] */
    private $extensions;

    /** @param Extension[] $extensions */
    public function __construct(array $extensions)
    {
        $this->extensions = $extensions;
    }

    public function __toString(): string
    {
        return serialize($this->extensions);
    }

    public function getHash(): string
    {
        return md5((string) $this);
    }
}
