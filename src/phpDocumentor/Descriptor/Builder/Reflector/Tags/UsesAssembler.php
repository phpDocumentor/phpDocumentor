<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use phpDocumentor\Compiler\Linker\Linker;
use phpDocumentor\Descriptor\Builder\Reflector\AssemblerAbstract;
use phpDocumentor\Descriptor\Tag\UsesDescriptor;
use phpDocumentor\Reflection\DocBlock\Context;
use phpDocumentor\Reflection\DocBlock\Tag\UsesTag;
use phpDocumentor\Reflection\DocBlock\Type\Collection;

class UsesAssembler extends AssemblerAbstract
{
    /**
     * Creates a new Descriptor from the given Reflector.
     *
     * @param UsesTag $data
     *
     * @return UsesDescriptor
     */
    public function create($data)
    {
        $descriptor = new UsesDescriptor($data->getName());
        $descriptor->setDescription($data->getDescription());
        $reference = $data->getReference();
        $context = $data->getDocBlock() ? $data->getDocBlock()->getContext() : null;

        if ($reference !== 'self'
            && $reference !== '$this'
            && $reference[0] !== '\\'
        ) {
            // Expand FQCN part of the FQSEN
            $referenceParts = explode('::', $reference);

            if (count($referenceParts) > 1 || $this->referenceIsNamespaceAlias($reference, $context)) {
                $referenceParts = $this->setFirstReferencePartAsType($context, $referenceParts);
            } elseif (isset($reference[0])) {
                array_unshift($referenceParts, Linker::CONTEXT_MARKER);
            }

            $reference = implode('::', $referenceParts);
        }

        $descriptor->setReference($reference);

        return $descriptor;
    }

    /**
     * @param Context $context
     * @param string[] $referenceParts
     * @return array The returned array will consist of a Collection object with the type, and strings for methods, etc.
     */
    private function setFirstReferencePartAsType($context, $referenceParts)
    {
        $type = current($referenceParts);
        $type = new Collection(
            array($type),
            $context
        );
        $referenceParts[0] = $type;
        return $referenceParts;
    }

    /**
     * When you have a relative reference to a class, we need to check if this class exists in the namespace aliases
     *
     * @param string $reference
     * @param Context $context
     * @return bool
     */
    private function referenceIsNamespaceAlias($reference, $context)
    {
        /** @var \phpDocumentor\Reflection\DocBlock\Context $context*/
        foreach ($context->getNamespaceAliases() as $alias) {
            if (substr($alias, -strlen($reference)) === $reference) {
                return true;
            }
        }
    }
}
