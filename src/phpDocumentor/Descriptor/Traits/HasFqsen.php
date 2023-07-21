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

namespace phpDocumentor\Descriptor\Traits;

use phpDocumentor\Reflection\Fqsen;

trait HasFqsen
{
    /** @var ?Fqsen Fully Qualified Structural Element Name; the FQCN including method, property or constant name */
    protected Fqsen|null $fqsen = null;

    /**
     * Sets the Fully Qualified Structural Element Name (FQSEN) for this element.
     *
     * @internal should not be called by any other class than the assemblers
     */
    public function setFullyQualifiedStructuralElementName(Fqsen $name): void
    {
        $this->fqsen = $name;
    }

    /**
     * Returns the Fully Qualified Structural Element Name (FQSEN) for this element.
     */
    public function getFullyQualifiedStructuralElementName(): Fqsen|null
    {
        return $this->fqsen;
    }
}
