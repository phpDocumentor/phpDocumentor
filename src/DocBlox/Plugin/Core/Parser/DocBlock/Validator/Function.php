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
        $valid = true;

        if (!$this->hasDocBlock()) return false;
        $this->hasShortDescription();
        $this->validateArguments();

        return $valid;
    }

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

    public function hasShortDescription()
    {
        if ('' !== trim($this->docblock->getShortDescription())) {
            return;
        }

        $type = $this instanceof DocBlox_Plugin_Core_Parser_DocBlock_Validator_Method
            ? 'method'
            : 'function';

        $this->logParserError(
            'CRITICAL',
            'No short description for ' . $type . ' ' . $this->entityName . '()',
            $this->lineNumber
        );
    }

    protected function validateArguments()
    {
        /** @var DocBlox_Reflection_Function $source  */
        $source = $this->source;
        $params = $this->docblock->getTagsByName('param');
        $arguments = $source->getArguments();

        foreach (array_values($arguments) as $key => $argument) {
            if (!$this->isArgumentInDocBlock($key, $argument, $params))
                continue;

            if (!$this->doesArgumentNameMatchParam($params[$key], $argument))
                continue;
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
            'NOTICE',
            'Name of argument ' . $argument->getName() . 'does not match with '
            . 'the DocBlock\'s name '
            . $param_name .' of ' . $this->entityName . '()',
            $argument->getLineNumber()
        );

        return false;
    }

    protected function isArgumentInDocBlock(
        $index, DocBlox_Reflection_Argument $argument, $params
    ) {
        /** @var DocBlox_Reflection_DocBlock_Tag $params  */
        if (isset($params[$index])) {
            return true;
        }

        $this->logParserError(
            'NOTICE',
            'Argument ' . $argument->getName() . ' is missing from '
            . 'the Docblock of ' . $this->entityName . '()',
            $argument->getLineNumber()
        );
        return false;
    }
}