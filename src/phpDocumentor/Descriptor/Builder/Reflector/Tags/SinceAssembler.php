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

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use phpDocumentor\Descriptor\Builder\Reflector\AssemblerAbstract;
use phpDocumentor\Descriptor\Tag\SinceDescriptor;
use phpDocumentor\Reflection\DocBlock\Tags\Since;

/**
 * Constructs a new descriptor from the Reflector for an `@since` tag.
 *
 * This object will read the reflected information for the `@since` tag and create a {@see SinceDescriptor} object that
 * can be used in the rest of the application and templates.
 */
class SinceAssembler extends AssemblerAbstract
{
    /**
     * Creates a new Descriptor from the given Reflector.
     *
     * @param Since $data
     *
     * @return SinceDescriptor
     */
    public function create($data)
    {
        $descriptor = new SinceDescriptor($data->getName());
        $descriptor->setDescription($data->getDescription());
        $descriptor->setVersion($data->getVersion());

        return $descriptor;
    }
}
