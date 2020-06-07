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

use phpDocumentor\Descriptor\ArgumentDescriptor;
use phpDocumentor\Descriptor\Collection;

/**
 * Describes the public interface of the description for a method.
 */
interface MethodInterface extends ElementInterface, TypeInterface
{
    public function setAbstract(bool $abstract) : void;

    public function isAbstract() : bool;

    /**
     * @return Collection<ArgumentDescriptor>
     */
    public function getArguments() : Collection;

    public function setFinal(bool $final) : void;

    public function isFinal() : bool;

    public function setStatic(bool $static) : void;

    public function isStatic() : bool;

    public function setVisibility(string $visibility) : void;
}
