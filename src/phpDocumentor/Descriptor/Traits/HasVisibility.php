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

use phpDocumentor\Descriptor\ValueObjects\Visibility;
use phpDocumentor\Descriptor\ValueObjects\VisibilityModifier;

trait HasVisibility
{
    protected Visibility|null $visibility = null;

    public function setVisibility(Visibility $visibility): void
    {
        $this->visibility = $visibility;
    }

    public function getVisibility(): Visibility
    {
        if ($this->visibility === null) {
            $this->visibility = new Visibility(VisibilityModifier::PUBLIC);
        }

        return $this->visibility;
    }
}
