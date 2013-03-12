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

namespace phpDocumentor\Compiler\Linker\Rule;

use phpDocumentor\Compiler\Linker\Rule;
use phpDocumentor\Descriptor\DescriptorAbstract;

/**
 * Linker rule instructing the linker to replace a given field's contents with a Descriptor.
 *
 * This rule inspects a given field of the the provided Descriptor and attempts to determine whether this is a FQSEN
 * by reading the first character. If the first character is a backslash ('\') than a FQSEN is assumed and this rule
 * will attempt to find the given FQSEN in the 'elements' index of the ProjectDescriptor.
 *
 * This rule may declare which type of element is intended, if it is provided than a parser error must be given if an
 * element is found in the 'elements' index but it does not match the intended type.
 *
 * If the field contents is not an FQSEN or the element is not found, the contents are left unchanged so that they may
 * be interpreted by the router during the building of the templates.
 */
class Replace extends Rule
{
    /**
     * Defines the type that limits this replace operation.
     *
     * @var string|null $type The local class name of a Descriptor without the suffix Descriptor, such as `Class`.
     */
    protected $type;

    /**
     * Name of the field to inspect.
     *
     * This name is used to inspect whether it is a FQSEN and if to to set a the descriptor.
     * As such the Descriptor must contain a getter and a setter that match the given field name prefixed with get
     * or set exactly.
     *
     * @var string field Name of the field to replace, such as `Type`.
     */
    protected $field;

    /**
     * Inspects the field for the given Descriptor and replaces its contents.
     *
     * @param DescriptorAbstract $descriptor
     *
     * @return void
     */
    public function execute(DescriptorAbstract $descriptor)
    {
    }

    /**
     * Returns the textual representation of the type that the FQSEN represents, or null to ignore typehinting.
     *
     * @return string|null The local class name of a Descriptor without the suffix Descriptor, such as `Class`.
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the type that the FQSEN must have, otherwise an error is thrown.
     *
     * @param string $type The local class name of a Descriptor without the suffix Descriptor, such as `Class`.
     *
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Returns the name of the field which' contents are to be replaced.
     *
     * @return string Name of the field to replace, such as `Type`.
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Sets the name of the field which' contents are to be replaced.
     *
     * @param string $field Name of the field to replace, such as `Type`.
     *
     * @return void
     */
    public function setField($field)
    {
        $this->field = $field;
    }
}
