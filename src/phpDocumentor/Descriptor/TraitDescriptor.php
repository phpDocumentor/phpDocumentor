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

/**
 * Descriptor representing a Trait.
 *
 * @api
 * @package phpDocumentor\AST
 */
class TraitDescriptor extends DescriptorAbstract implements Interfaces\TraitInterface
{
    use Traits\HasAttributes;
    use Traits\HasProperties;
    use Traits\HasMethods;
    use Traits\UsesTraits;
    use Traits\HasConstants;
}
