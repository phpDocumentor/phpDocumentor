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

namespace phpDocumentor\Guides\NodeRenderers;

use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Nodes\Node;

use function get_class;
use function is_callable;
use function is_string;

class DefaultNodeRenderer implements NodeRenderer
{
    /** @var Environment */
    private $environment;

    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    public function render(Node $node): string
    {
        $value = $node->getValue();

        if ($value instanceof Node) {
            return $this->environment->getNodeRendererFactory()
                ->get(get_class($value))
                ->render($value);
        }

        if (is_string($value)) {
            return $value;
        }

        if (is_callable($value)) {
            return ($value)();
        }

        return '';
    }
}
