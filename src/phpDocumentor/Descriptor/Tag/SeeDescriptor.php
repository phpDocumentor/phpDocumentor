<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
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
    public function setReference($reference)
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
