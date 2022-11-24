<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Traits;

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

use phpDocumentor\Descriptor\Interfaces\ElementInterface;
use phpDocumentor\Descriptor\Interfaces\InheritsFromElement;

use function strtolower;
use function trim;

trait HasSummary
{
    /** @var string $summary A summary describing the function of this element in short. */
    protected string $summary = '';

    /**
     * Sets the summary describing this element in short.
     *
     * @internal should not be called by any other class than the assamblers
     */
    public function setSummary(string $summary): void
    {
        $this->summary = $summary;
    }

    /**
     * Returns the summary which describes this element.
     *
     * This method will automatically attempt to inherit the parent's summary if this one has none.
     */
    public function getSummary(): string
    {
        if ($this->summary && strtolower(trim($this->summary)) !== '{@inheritdoc}') {
            return $this->summary;
        }

        if ($this instanceof InheritsFromElement) {
            $parent = $this->getInheritedElement();
            if ($parent instanceof ElementInterface) {
                return $parent->getSummary();
            }
        }

        return $this->summary;
    }
}
