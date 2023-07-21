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

/**
 * Interface representing the common interface for all elements that can contain sub-elements.
 */
interface ContainerInterface
{
    /** @return Collection<ConstantInterface> */
    public function getConstants(): Collection;

    /** @return Collection<FunctionInterface> */
    public function getFunctions(): Collection;

    /** @return Collection<ClassInterface> */
    public function getClasses(): Collection;

    /** @return Collection<InterfaceInterface> */
    public function getInterfaces(): Collection;

    /** @return Collection<TraitInterface> */
    public function getTraits(): Collection;

    /** @return Collection<EnumInterface> */
    public function getEnums(): Collection;
}
