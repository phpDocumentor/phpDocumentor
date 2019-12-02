<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\Collection;

/**
 * Common interface representing the description of a class.
 *
 * @see NamespaceInterface Classes may be contained in namespaces.
 * @see FileInterface      Classes may be defined in a file.
 */
interface ClassInterface extends ElementInterface, ChildInterface, TypeInterface
{
    public function setInterfaces(Collection $interfaces) : void;

    public function getInterfaces() : Collection;

    public function setFinal(bool $final) : void;

    public function isFinal() : bool;

    public function setAbstract(bool $abstract) : void;

    public function isAbstract() : bool;

    public function setConstants(Collection $constants) : void;

    public function getConstants() : Collection;

    public function setMethods(Collection $methods) : void;

    public function getMethods() : Collection;

    public function getInheritedMethods() : Collection;

    public function setProperties(Collection $properties) : void;

    public function getProperties() : Collection;

    public function getInheritedProperties() : Collection;
}
