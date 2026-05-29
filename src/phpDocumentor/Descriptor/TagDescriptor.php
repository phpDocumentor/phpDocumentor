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

namespace phpDocumentor\Descriptor;

use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;
use phpDocumentor\Descriptor\Filter\Filterable;
use phpDocumentor\Descriptor\Interfaces\DocBlock\TagInterface;
use Stringable;

/**
 * Base class for any tag descriptor and used when a tag has no specific descriptor.
 *
 * @api
 * @package phpDocumentor\AST
 */
class TagDescriptor implements Descriptor, Filterable, Stringable, TagInterface
{
    use Traits\HasName;
    use Traits\HasDescription;
    use Traits\HasErrors;

    /**
     * Initializes the tag by setting the name and errors,
     */
    public function __construct(string $name, DescriptionDescriptor|null $description = null)
    {
        $this->setName($name);
        $this->setDescription($description);
    }

    public function __toString(): string
    {
        return (string) $this->description;
    }
}
