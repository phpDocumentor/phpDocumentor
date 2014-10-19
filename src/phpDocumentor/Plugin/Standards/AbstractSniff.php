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

namespace phpDocumentor\Plugin\Standards;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Validator;

/**
 * Represents the base functionality of a Sniff.
 *
 * A Sniff is a definition of the business side of a Rule and determines what a rule may do. The actual rule in a
 * ruleset will only determine which of these sniffs actually get executed.
 *
 * The sniff will receive a Symfony Validator, the identifying rule name for this Sniff and to which Descriptor it is
 * bound. This means that a single sniff may be reused to verify different types of Descriptors and still have a
 * different rule name. That will enable the standard to finely define which Sniffs should be ran and gives the
 * developers the power to reuse a sniff.
 *
 *     The above might mean that if both a File and a Class share a similar sniff but you want to give the rule a
 *     different name so you can determine in your ruleset to exclude on or the other than that is possible.
 *
 * This system works because in the (abstract) {@see self::getConstraint()} method a Symfony Constraint is created
 * (and returned), and if a rule with the name in this sniff is present in a ruleset it will be enabled.
 *
 * Using the {@see self::$property} property you can even add the constraint onto a property instead of class-wide.
 */
abstract class AbstractSniff
{
    /**
     * @var string|null if the constraint should check a property specifically instead of the entire Descriptor than
     *     that can be specified by overriding this property and passing the name of the property to check.
     */
    protected $property = null;

    /** @var Validator where the Constraint is applied on */
    private $validator;

    /** @var string name of the rule that can be used to enable this Sniff */
    private $name;

    /** @var string the class of the Descriptor that is to be covered by an instance of this sniff */
    private $descriptorClass;

    /**
     * Initializes this sniff with all dependencies.
     *
     * @param Validator $validator
     * @param string    $name
     * @param string    $descriptorClass May be a QCN (Class without leading '\', or a simple name. If a simple name
     *     if given than `phpDocumentor\Descriptor\` is prepended and 'Descriptor' is appended.
     */
    public function __construct(Validator $validator, $name, $descriptorClass)
    {
        $this->validator       = $validator;
        $this->name            = $name;
        $this->descriptorClass = class_exists($descriptorClass)
            ? $descriptorClass
            : 'phpDocumentor\\Descriptor\\' . $descriptorClass . 'Descriptor';
    }

    /**
     * Returns the identifying rule name with which this Sniff can be enabled.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Enables the sniff.
     *
     * This method ensures that the Constraint, as returned by {@see self::getConstraint()}, is applied on all
     * Descriptor objects that are of the type specified in {@see self::$descriptorClass}.
     *
     * @return void
     */
    public function enable()
    {
        if ($this->property) {
            $this->getMetaData()->addPropertyConstraint($this->property, $this->getConstraint());
        } else {
            $this->getMetaData()->addConstraint($this->getConstraint());
        }
    }

    /**
     * Returns a Constraint Object that is responsible for the actual business rules in this sniff.
     *
     * A Constraint Object is used to validate a property value or combination of values in a Descriptor. You can use
     * all the [built-in Constraints of Symfony](http://symfony.com/doc/current/reference/constraints.html) or create
     * [your own](http://symfony.com/doc/current/cookbook/validation/custom_constraint.html).
     *
     *     *Important*: the 'message' property of the constraint MUST contain the identifying rule name as found in
     *     the {@see self::getName()} method. This value is used during the Violation collection to be able to
     *     find the Rule to which this Constraint (or actually its violation) pertains.
     *
     *     This is a limitation of the Symfony Validation Framework where a Violation does not know which Constraint
     *     has risen it and as such we have no other means than the message to re-discover the associated Rule and/or
     *     Sniff.
     *
     * @see \phpDocumentor\Descriptor\ProjectDescriptorBuilder::validate() where the Violations are read and converted
     *     into errors, including the right severity and message.
     *
     * @return Constraint
     */
    abstract protected function getConstraint();

    /**
     * This method finds the Metadata class for the given Descriptor.
     *
     * The Metadata class is where the Symfony Validator prepares the Constraints on and is its way of knowing
     * beforehand which Descriptor has which Constraints.
     *
     * @return ClassMetadata
     */
    private function getMetaData()
    {
        return $this->validator->getMetadataFor($this->descriptorClass);
    }
}
