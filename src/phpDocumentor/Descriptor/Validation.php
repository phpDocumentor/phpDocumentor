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

namespace phpDocumentor\Descriptor;

use Psr\Log\LogLevel;
use Zend\I18n\Translator\Translator;
use phpDocumentor\Descriptor\Validator\Error;
use phpDocumentor\Descriptor\Validator\ValidatorInterface;
use phpDocumentor\Reflection\ReflectionAbstract;

/**
 * Facilitates the validation of Structural elements and their DocBlocks by providing grouping flyweight validators
 * per type of DocBlock.
 *
 * Each type of element has a distinct scheme for its DocBlocks, for example a File DocBlock has different requirements
 * than a Function DocBlock. This validation component is capable of sending aggregated log results to the logger so
 * that individual validators do not need to concern with logging an internationalization.
 *
 * The Validation component also allows users to disable specific errors or all validators for a group of DocBlocks.
 */
class Validation
{
    const TYPE_FILE      = 'file';
    const TYPE_CONSTANT  = 'constant';
    const TYPE_FUNCTION  = 'function';
    const TYPE_CLASS     = 'class';
    const TYPE_INTERFACE = 'interface';
    const TYPE_TRAIT     = 'trait';
    const TYPE_PROPERTY  = 'property';
    const TYPE_METHOD    = 'method';
    const TYPE_ARGUMENT  = 'argument';

    protected $reflectorClassToTypeMap = array(
        'phpDocumentor\Reflection\FileReflector'                       => self::TYPE_FILE,
        'phpDocumentor\Reflection\ConstantReflector'                   => self::TYPE_CONSTANT,
        'phpDocumentor\Reflection\FunctionReflector'                   => self::TYPE_FUNCTION,
        'phpDocumentor\Reflection\ClassReflector'                      => self::TYPE_CLASS,
        'phpDocumentor\Reflection\InterfaceReflector'                  => self::TYPE_INTERFACE,
        'phpDocumentor\Reflection\TraitReflector'                      => self::TYPE_TRAIT,
        'phpDocumentor\Reflection\ClassReflector\PropertyReflector'    => self::TYPE_PROPERTY,
        'phpDocumentor\Reflection\ClassReflector\MethodReflector'      => self::TYPE_METHOD,
        'phpDocumentor\Reflection\FunctionReflector\ArgumentReflector' => self::TYPE_ARGUMENT,
    );

    /** @var callable[][] $validators*/
    protected $validators = array();

    /** @var Translator $translator */
    protected $translator;

    /**
     * Initializes this validation service with the translator.
     *
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Registers a validator with a given code to a type of element.
     *
     * A validator is a callable element that should return true or false; if the validator returns true
     * than the given code is passed through the translator and the resulting error message is logged.
     *
     * @param string|string[]                                             $type      One or more of the pre-defined
     *     types (see class constants).
     * @param callable|ValidatorInterface|(callable|ValidatorInterface)[] $validator A callable that will return an
     *     Error object to determine whether validation was successful.
     *
     * @throws \InvalidArgumentException if the type does not match any of the predefined types.
     * @throws \InvalidArgumentException if the code contains only digits or is not a string.
     * @throws \InvalidArgumentException if the validator is not a callable.
     *
     * @return void
     */
    public function register($type, $validator)
    {
        $availableTypes = array_values($this->reflectorClassToTypeMap);
        $types          = (!is_array($type)) ? array($type) : $type;
        $validators     = (!is_array($validator)) ? array($validator) : $validator;

        if (array_diff($types, $availableTypes)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Provided type "%s" does not match one of the following: %s',
                    $type,
                    implode(', ', $availableTypes)
                )
            );
        }

        foreach ($validators as $validator) {
            if (!is_callable($validator) && !$validator instanceof ValidatorInterface) {
                throw new \InvalidArgumentException(
                    'A validator for the parser must be callable or an instance of the Validator Interface'
                );
            }

            foreach ($types as $type) {
                $this->validators[$type][] = $validator;
            }
        }
    }

    /**
     *
     *
     * @param ReflectionAbstract $element
     *
     * @return array
     */
    public function validate($element)
    {
        $result = array();

        $type = isset($this->reflectorClassToTypeMap[get_class($element)])
            ? $this->reflectorClassToTypeMap[get_class($element)]
            : null;

        if (isset($this->validators[$type])) {
            foreach ($this->validators[$type] as $validator) {
                $error = ($validator instanceof ValidatorInterface)
                    ? $validator->validate($element)
                    : $validator($element);

                /** @var Error $error */
                if ($error) {
                    $result[] = $error;

                    // if the severity of an error is critical or worse then processing must be stopped
                    // as it might affect other validations.
                    $breakingSeverity = array(LogLevel::CRITICAL, LogLevel::EMERGENCY);
                    if (in_array($error->getSeverity(), $breakingSeverity)) {
                        break;
                    }
                }

            }
        }

        return $result;
    }
}
