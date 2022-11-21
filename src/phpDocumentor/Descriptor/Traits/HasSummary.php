<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Traits;

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

        $parent = $this->getInheritedElement();
        if ($parent instanceof self) {
            return $parent->getSummary();
        }

        return $this->summary;
    }
}
