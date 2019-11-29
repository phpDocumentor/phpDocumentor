<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\Collection;

/**
 * Describes the public interface for a description of a File.
 */
interface FileInterface extends ElementInterface, ContainerInterface
{
    public function getHash() : string;

    public function setSource(?string $source) : void;

    public function getSource() : ?string;

    public function getNamespaceAliases() : Collection;

    public function getIncludes() : Collection;

    public function getErrors() : Collection;
}
