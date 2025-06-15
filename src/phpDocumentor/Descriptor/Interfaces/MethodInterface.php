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
use phpDocumentor\Descriptor\Tag\ParamDescriptor;
use phpDocumentor\Descriptor\Tag\ReturnDescriptor;
use phpDocumentor\Descriptor\ValueObjects\Visibility;

/**
 * Describes the public interface of the description for a method.
 */
interface MethodInterface extends
    ElementInterface,
    TypeInterface,
    InheritsFromElement,
    ChildInterface,
    AttributedInterface,
    VisibilityInterface
{
    public function setAbstract(bool $abstract): void;

    public function isAbstract(): bool;

    /** @return Collection<ArgumentInterface> */
    public function getArguments(): Collection;

    public function setFinal(bool $final): void;

    public function isFinal(): bool;

    public function setStatic(bool $static): void;

    public function isStatic(): bool;

    public function setVisibility(Visibility $visibility): void;

    /** @return Collection<ParamDescriptor> */
    public function getParam(): Collection;

    /** @return Collection<ReturnDescriptor> */
    public function getReturn(): Collection;
}
