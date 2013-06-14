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

namespace phpDocumentor\Descriptor\Builder;

use phpDocumentor\Descriptor\ArgumentDescriptor;
use phpDocumentor\Descriptor\Builder\Reflector\ClassAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\ConstantAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\FunctionAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\InterfaceAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\PropertyAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\TraitAssembler;
use phpDocumentor\Descriptor\BuilderAbstract;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\PropertyDescriptor;
use phpDocumentor\Descriptor\Tag\ParamDescriptor;
use phpDocumentor\Descriptor\Tag\TagFactory;
use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\Descriptor\Validation;
use phpDocumentor\Reflection\BaseReflector;
use phpDocumentor\Reflection\ClassReflector;
use phpDocumentor\Reflection\ConstantReflector;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\FileReflector;
use phpDocumentor\Reflection\FunctionReflector;
use phpDocumentor\Reflection\InterfaceReflector;
use phpDocumentor\Reflection\TraitReflector;

/**
 * Builds a Project Descriptor using the information from the Reflection component.
 *
 * The Descriptors are a light representation of the element structure in a project, also known as AST
 * (Abstract Syntax Tree). In order to build this representation from the Reflection API of phpDocumentor
 * can this builder be used.
 *
 * The most straightforward usage is to pass each processed File (in the form of a FileReflector object) to the
 * {@see buildFile()} method. This will extract the file's meta data and child elements (and their child elements),
 * build a FileDescriptor and inject that into the ProjectDescriptor that is created (or passed) as part of this
 * builder (see {@see BuilderAbstract::__construct()}).
 *
 * Example:
 *
 * ```
 * $reflector = new \phpDocumentor\Descriptor\Builder\Reflector();
 * $reflector->buildFile($fileReflector);
 * $projectDescriptor = $reflector->getProjectDescriptor();
 * ```
 *
 * It is also possible to convert each individual element using their respective *build* method but this will not
 * automatically link the element to the correct file.
 *
 * This builder is also capable of validating each Reflector's properties (usually DocBlocks) and populate the *errors*
 * of the linked Descriptor. This only occurs when a Validation manager is supplied, see {@see setValidation()} for
 * more information.
 *
 * @todo Consider moving the individual build* method's contents to mapper classes, they map Reflectors to Descriptors
 */
class Reflector extends BuilderAbstract
{
    /**
     *
     *
     * @param DescriptorAbstract $descriptor
     *
     * @return DescriptorAbstract| null
     */
    protected function filterInternalTag($descriptor)
    {
        // if internal elements are not allowed; do not add this element
        if ($descriptor->getTags()->get('internal')
            && !$this->isVisibilityAllowed(ProjectDescriptor\Settings::VISIBILITY_INTERNAL)
        ) {
            return null;
        }

        return $descriptor;
    }
}
