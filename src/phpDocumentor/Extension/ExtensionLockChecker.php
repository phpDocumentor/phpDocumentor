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
use Symfony\Component\Config\ResourceCheckerInterface;

use Webmozart\Assert\Assert;
use function md5;
use function serialize;

final class ExtensionLockChecker implements ResourceCheckerInterface
{
    /** @var Manifest[] */
    private $manifests;

    /** @param Manifest[] $manifests */
    public function __construct(array $manifests)
    {
        $this->manifests = $manifests;
    }

    public function supports(ResourceInterface $metadata): bool
    {
        return $metadata instanceof ExtensionsResource;
    }

    public function isFresh(ResourceInterface $resource, int $timestamp): bool
    {
        Assert::isInstanceOf($resource,ExtensionsResource::class);

        return $resource->getHash() === md5(serialize($this->manifests));
    }
}
