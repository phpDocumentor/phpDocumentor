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

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\NamespaceDescriptor;
use phpDocumentor\Reflection\Php\Namespace_;
use function strlen;
use function substr;

/**
 * @extends AssemblerAbstract<NamespaceDescriptor, Namespace_>
 */
final class NamespaceAssembler extends AssemblerAbstract
{
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param Namespace_ $data
     */
    public function create(object $data) : NamespaceDescriptor
    {
        $descriptor = new NamespaceDescriptor();
        $descriptor->setName($data->getName());
        $descriptor->setFullyQualifiedStructuralElementName($data->getFqsen());
        $namespace = substr((string) $data->getFqsen(), 0, -strlen($data->getName()) - 1);
        $descriptor->setNamespace($namespace === '' ? '\\' : $namespace);

        return $descriptor;
    }
}
