<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\Reflection\TraitReflector;

/**
 * Assembles an TraitDescriptor using an TraitReflector.
 */
class TraitAssembler extends AssemblerAbstract
{
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param TraitReflector $data
     *
     * @return TraitDescriptor
     */
    public function create($data)
    {
        $traitDescriptor = new TraitDescriptor();

        $traitDescriptor->setFullyQualifiedStructuralElementName($data->getName());
        $traitDescriptor->setName($data->getShortName());
        $traitDescriptor->setNamespace('\\' . $data->getNamespace());

        $this->assembleDocBlock($data, $traitDescriptor);

        $traitDescriptor->setLocation('', $data->getLinenumber());

        return $traitDescriptor;
    }
}
