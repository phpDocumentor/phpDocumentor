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

namespace phpDocumentor\Descriptor\Tag;

use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\TagDescriptor;

class SeeDescriptor extends TagDescriptor
{
    /** @var DescriptorAbstract|string $reference */
    protected $reference = '';

    /**
     * @param DescriptorAbstract|string $reference
     */
    public function setReference($reference) : void
    {
        $this->reference = $reference;
    }

    /**
     * @return DescriptorAbstract|string
     */
    public function getReference()
    {
        return $this->reference;
    }
}
