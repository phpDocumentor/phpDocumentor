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

namespace phpDocumentor\Transformer\Template;

use InvalidArgumentException;

use function sprintf;

/**
 * @codeCoverageIgnore not worth it
 */
final class TemplateNotFound extends InvalidArgumentException
{
    public function __construct(string $template)
    {
        parent::__construct(sprintf('The given template %s could not be found or is not readable', $template));
    }
}
