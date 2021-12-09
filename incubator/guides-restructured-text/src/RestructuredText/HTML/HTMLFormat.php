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

namespace phpDocumentor\Guides\RestructuredText\HTML;

use IteratorAggregate;
use phpDocumentor\Guides\NodeRenderers\NodeRendererFactory;
use phpDocumentor\Guides\RestructuredText\OutputFormat;

final class HTMLFormat extends OutputFormat
{
    /** @var NodeRendererFactory */
    private $nodeRendererFactory;

    /**
     * @todo Refactor Renderer out of the formats; it complicates parser instantiation because the Parser becomes
     *       coupled with the renderer this way
     */
    public function __construct(
        string $fileExtension,
        IteratorAggregate $directives,
        NodeRendererFactory $nodeRendererFactory
    ) {
        parent::__construct($fileExtension, $directives);
        $this->nodeRendererFactory = $nodeRendererFactory;
    }

    public function getNodeRendererFactory(): NodeRendererFactory
    {
        return $this->nodeRendererFactory;
    }
}
