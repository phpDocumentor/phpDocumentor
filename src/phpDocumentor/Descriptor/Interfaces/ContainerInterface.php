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

use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\TraitDescriptor;

/**
 * Interface representing the common interface for all elements that can contain sub-elements.
 */
interface ContainerInterface
{
    /**
     * @return Collection<ConstantDescriptor>
     */
    public function getConstants(): Collection;

    /**
     * @return Collection<FunctionDescriptor>
     */
    public function getFunctions(): Collection;

    /**
     * @return Collection<ClassDescriptor>
     */
    public function getClasses(): Collection;

    /**
     * @return Collection<InterfaceDescriptor>
     */
    public function getInterfaces(): Collection;

    /**
     * @return Collection<TraitDescriptor>
     */
    public function getTraits(): Collection;
}
