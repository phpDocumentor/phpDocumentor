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

namespace phpDocumentor\Guides;

use phpDocumentor\Guides\NodeRenderers\NodeRendererFactory;
use phpDocumentor\Guides\Nodes\DocumentNode;

interface Parser
{
    public function getEnvironment(): Environment;

    public function getReferenceBuilder(): ReferenceBuilder;

    public function getNodeRendererFactory(): NodeRendererFactory;

    public function parse(Environment $environment, string $contents): DocumentNode;

    public function getDocument(): DocumentNode;
}
