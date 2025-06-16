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

namespace phpDocumentor\Descriptor;

use phpDocumentor\Descriptor\Interfaces\AttributedInterface;
use phpDocumentor\Descriptor\Interfaces\ClassInterface;
use phpDocumentor\Reflection\Fqsen;

/**
 * Descriptor representing a Class.
 *
 * @api
 * @package phpDocumentor\AST
 */
class ClassDescriptor extends DescriptorAbstract implements Interfaces\ClassInterface, AttributedInterface
{
    use Traits\CanBeFinal;
    use Traits\CanBeAbstract;
    use Traits\HasMethods;
    use Traits\HasProperties;
    use Traits\HasConstants;
    use Traits\ExtendsClass;
    use Traits\ImplementsInterfaces;
    use Traits\UsesTraits;
    use Traits\HasAttributes;

    /** @var bool $readOnly Whether this class is marked as readonly. */
    protected bool $readOnly = false;

    /** @internal should not be called by any other class than the assemblers */
    public function setReadOnly(bool $readOnly): void
    {
        $this->readOnly = $readOnly;
    }

    public function isReadOnly(): bool
    {
        return $this->readOnly;
    }

    /** @return ClassInterface|Fqsen|string|null */
    public function getInheritedElement()
    {
        return $this->getParent();
    }
}
