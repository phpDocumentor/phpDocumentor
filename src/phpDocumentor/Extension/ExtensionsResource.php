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
    /** @var Manifest[] */
    private $manifests;

    /** @param Manifest[] $manifests */
    public function __construct(array $manifests)
    {
        $this->manifests = $manifests;
    }

    public function __toString(): string
    {
        return serialize($this->manifests);
    }

    public function getHash(): string
    {
        return md5((string) $this);
    }
}
