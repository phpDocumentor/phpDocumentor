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

namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Reflection\Fqsen;

/**
 * Describes the public interface for a description of a File.
 */
interface FileInterface extends ElementInterface, ContainerInterface
{
    public function getHash(): string;

    public function setSource(string|null $source): void;

    public function getSource(): string|null;

    /** @return Collection<NamespaceInterface|Fqsen> */
    public function getNamespaceAliases(): Collection;

    /** @return Collection<string> */
    public function getIncludes(): Collection;
}
