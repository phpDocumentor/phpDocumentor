<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Event;

use phpDocumentor\Event\EventAbstract;
use phpDocumentor\Transformer\Transformation;

/**
 * Event happening prior to each individual transformation.
 */
class PreTransformationEvent extends EventAbstract
{
    /** @var \DOMDocument remembers the XML-based AST so that it can be used from the listener */
    protected $source;

    /** @var Transformation */
    protected $transformation;

    /**
     * Returns the Abstract Syntax Tree as DOMDocument.
     */
    public function getSource(): ?\DOMDocument
    {
        return $this->source;
    }

    /**
     * Sets the Abstract Syntax Tree as DOMDocument.
     */
    public function setSource(\DOMDocument $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getTransformation(): ?Transformation
    {
        return $this->transformation;
    }

    public function setTransformation(Transformation $transformation): self
    {
        $this->transformation = $transformation;

        return $this;
    }
}
