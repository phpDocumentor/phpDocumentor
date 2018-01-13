<?php
/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 *  @license   http://www.opensource.org/licenses/mit-license.php MIT
 *  @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\NamespaceDescriptor;
use phpDocumentor\Reflection\Php\Namespace_;

final class NamespaceAssembler extends AssemblerAbstract
{
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param Namespace_ $data
     *
     * @return DescriptorAbstract|Collection
     */
    public function create($data)
    {
        $descriptor = new NamespaceDescriptor();
        $descriptor->setName($data->getName());
        $descriptor->setFullyQualifiedStructuralElementName($data->getFqsen());
        $namespace = substr($data->getFqsen(), 0, -strlen($data->getName()) - 1);
        $descriptor->setNamespace($namespace === '' ? '\\' : $namespace);

        return $descriptor;
    }
}
