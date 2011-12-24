<?php
/**
 * File contains the DocBlox_Core_Validator_Function class
 *
 * PHP Version 5
 *
 * @category   DocBlox
 * @package    Parser
 * @subpackage DocBlock_Validators
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
/**
 * This class is responsible for validating a function's docblock.
 *
 * @category   DocBlox
 * @package    Parser
 * @subpackage DocBlock_Validators
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Plugin_Core_Parser_DocBlock_Validator_Function
    extends DocBlox_Plugin_Core_Parser_DocBlock_Validator_Abstract
{
    /**
     * Is the docblock valid?
     *
     * @see DocBlox_Parser_DocBlock_Validator::isValid()
     *
     * @return boolean
     */
    public function isValid()
    {
        if (!$this->hasDocBlock()) return false;
        $this->hasShortDescription();
        $this->validateArguments();

        return true;
    }

    /**
     * Validates whether this element has a docblock.
     *
     * @return bool
     */
    protected function hasDocBlock()
    {
        if (null !== $this->docblock) {
            return true;
        }

        $type = $this instanceof DocBlox_Plugin_Core_Parser_DocBlock_Validator_Method
            ? 'method'
            : 'function';

        $this->logParserError(
            'ERROR',
            'No DocBlock was found for ' . $type . ' ' . $this->entityName . '()',
            $this->lineNumber
        );

        return false;
    }

    /**
     * Validates whether this function has a short description.
     *
     * @return bool
     */
    public function hasShortDescription()
    {
        if ('' !== trim($this->docblock->getShortDescription())) {
            return true;
        }

        $type = $this instanceof DocBlox_Plugin_Core_Parser_DocBlock_Validator_Method
            ? 'method'
            : 'function';

        $this->logParserError(
            'CRITICAL',
            'No short description for ' . $type . ' ' . $this->entityName . '()',
            $this->lineNumber
        );

        return false;
    }

    /**
     * Validates all arguments whether they align nicely with the docblocks.
     *
     * @return void
     */
    protected function validateArguments()
    {
        /** @var DocBlox_Reflection_Function $source  */
        $source = $this->source;
        $params = $this->docblock->getTagsByName('param');
        $arguments = $source->getArguments();

        foreach (array_values($arguments) as $key => $argument) {
            if (!$this->isArgumentInDocBlock($key, $argument, $params))
                continue;

            $this->doesArgumentNameMatchParam($params[$key], $argument);
            $this->doesArgumentTypehintMatchParam($params[$key], $argument);
        }

        foreach($params as $param) {
            $param_name = $param->getVariableName();
            if (isset($arguments[$param_name])) {
                continue;
            }

            $this->logParserError(
                'NOTICE',
                'Parameter ' . $param_name .' could not be found in '
                . $this->entityName . '()',
                $param->getLineNumber()
            );
        }
    }

    /**
     * Validates whether the name of the argument is the same as that of the
     * @param tag.
     *
     * If the @param tag does not contain a name then this method will set it
     * based on the argument.
     *
     * @param DocBlox_Reflection_DocBlock_Tag_Param $param    @param to validate
     *     with.
     * @param DocBlox_Reflection_Argument           $argument Argument to validate
     *     against.
     *
     * @return bool whether an issue occurred
     */
    protected function doesArgumentNameMatchParam(
        DocBlox_Reflection_DocBlock_Tag_Param $param,
        DocBlox_Reflection_Argument $argument
    ) {
        $param_name = $param->getVariableName();
        if ($param_name == $argument->getName()) {
            return true;
        }

        if ($param_name == '') {
            $param->setVariableName($argument->getName());
            return false;
        }

        $this->logParserError(
            'ERROR',
            'Name of argument ' . $argument->getName() . 'does not match with '
            . 'the DocBlock\'s name '
            . $param_name .' of ' . $this->entityName . '()',
            $argument->getLineNumber()
        );

        return false;
    }

    /**
     * Validates whether an argument is mentioned in the docblock.
     *
     * @param integer                         $index    The position in the
     *     argument listing.
     * @param DocBlox_Reflection_Argument     $argument The argument itself.
     * @param DocBlox_Reflection_DocBlock_Tag $params   The list of @param tags
     *     to validate against.
     *
     * @return bool whether an issue occurred.
     */
    protected function isArgumentInDocBlock(
        $index, DocBlox_Reflection_Argument $argument, $params
    ) {
        if (isset($params[$index])) {
            return true;
        }

        $this->logParserError(
            'ERROR',
            'Argument ' . $argument->getName() . ' is missing from '
            . 'the Docblock of ' . $this->entityName . '()',
            $argument->getLineNumber()
        );
        return false;
    }

    /**
     * Checks the typehint of the argument versus the @param tag.
     *
     * If the argument has no typehint we do not check anything. When multiple
     * type are given then the typehint needs to be one of them.
     *
     * @param DocBlox_Reflection_DocBlock_Tag_Param $param
     * @param DocBlox_Reflection_Argument           $argument
     *
     * @return bool whether an issue occurred
     */
    protected function doesArgumentTypehintMatchParam(
        DocBlox_Reflection_DocBlock_Tag_Param $param,
        DocBlox_Reflection_Argument $argument
    ) {
        if (!$argument->getType()
            || in_array($argument->getType(), $param->getTypes())
        ) {
            return true;
        }

        $this->logParserError(
            'ERROR',
            'The type hint of the argument is incorrect for the type '
            . 'definition of the @param tag with argument '
            . $argument->getName() . ' in ' . $this->entityName . '()',
            $argument->getLineNumber()
        );

        return false;
    }
}