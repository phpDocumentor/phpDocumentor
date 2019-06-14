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

namespace phpDocumentor\Transformer\Writer\Xml;

use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\NamespaceDescriptor;

/**
 * Converter used to create an XML Element representing the method, its arguments and its DocBlock.
 *
 * In order to convert the arguments and DocBlock to their XML representation this class requires their respective
 * converters.
 */
class MethodConverter
{
    /** @var ArgumentConverter */
    protected $argumentConverter;

    /** @var DocBlockConverter */
    protected $docBlockConverter;

    /**
     * Initializes this converter with the Argument and DocBlock converter.
     */
    public function __construct(ArgumentConverter $argumentConverter, DocBlockConverter $docBlockConverter)
    {
        $this->argumentConverter = $argumentConverter;
        $this->docBlockConverter = $docBlockConverter;
    }

    /**
     * Export the given reflected method definition to the provided parent element.
     *
     * @param \DOMElement      $parent Element to augment.
     * @param MethodDescriptor $method Element to export.
     *
     * @return \DOMElement
     */
    public function convert(\DOMElement $parent, MethodDescriptor $method)
    {
        $fullyQualifiedNamespaceName = $method->getNamespace() instanceof NamespaceDescriptor
            ? $method->getNamespace()->getFullyQualifiedStructuralElementName()
            : $parent->getAttribute('namespace');

        $child = new \DOMElement('method');
        $parent->appendChild($child);

        $child->setAttribute('final', var_export($method->isFinal(), true));
        $child->setAttribute('abstract', var_export($method->isAbstract(), true));
        $child->setAttribute('static', var_export($method->isStatic(), true));
        $child->setAttribute('visibility', $method->getVisibility());
        $child->setAttribute('namespace', $fullyQualifiedNamespaceName);
        $child->setAttribute('line', (string) $method->getLine());

        $child->appendChild(new \DOMElement('name', $method->getName()));
        $child->appendChild(new \DOMElement('full_name', $method->getFullyQualifiedStructuralElementName()));

        $this->docBlockConverter->convert($child, $method);

        foreach ($method->getArguments() as $argument) {
            $this->argumentConverter->convert($child, $argument);
        }

        return $child;
    }
}
