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

namespace phpDocumentor\Descriptor;

abstract class DocumentationSetDescriptor
{
    /** @var string */
    protected $name = '';

    /** @var array */
    protected $source = [];

    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Returns the source location for this set of documentation.
     *
     * The returned array contains an element 'dsn' (of type DSN) and 'paths'; where each path represents one part
     * of the documentation set relative to the DSN.
     *
     * @todo: should the source location be included in a Descriptor? This couples it to the file system upon which
     *   it was ran and makes it uncacheable. But should this be cached? In any case, I need it for the RenderGuide
     *   writer at the moment; so refactor this once that becomes clearer.
     */
    public function getSource() : array
    {
        return $this->source;
    }
}
