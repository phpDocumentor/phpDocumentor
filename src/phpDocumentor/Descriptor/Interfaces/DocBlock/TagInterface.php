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

namespace phpDocumentor\Descriptor\Interfaces\DocBlock;

use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Descriptor\Filter\Filterable;
use phpDocumentor\Descriptor\Interfaces\Collection;
use phpDocumentor\Descriptor\Validation\Error;
use Stringable;

/**
 * Base class for any tag descriptor and used when a tag has no specific descriptor.
 *
 * @api
 * @package phpDocumentor\AST
 */
interface TagInterface extends Descriptor, Filterable, Stringable
{
    /**
     * Returns all errors associated with this tag.
     *
     * @return Collection<Error>
     */
    public function getErrors(): Collection;
}
