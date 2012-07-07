<?php
/**
 * File contains the
 * \phpDocumentor\Plugin\Core\Parser\DocBlock\Validator\FunctionValidator class.
 *
 * PHP Version 5
 *
 * @category   phpDocumentor
 * @package    Parser
 * @subpackage DocBlock_Validators
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core\Parser\DocBlock\Validator;

/**
 * This class is responsible for validating a function's docblock.
 *
 * @category   phpDocumentor
 * @package    Parser
 * @subpackage DocBlock_Validators
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
class FunctionValidator extends ValidatorAbstract
{
    /**
     * Is the docblock valid?
     *
     * @see phpDocumentor_Parser_DocBlock_Validator::isValid()
     *
     * @return boolean
     */
    public function isValid()
    {
        if (!$this->hasDocBlock())
        {
            return false;
        }
        $this->hasShortDescription();
        $this->validateArguments();

        if ($this->docblock->hasTag('return')) {
            $this->isDefaultIdeType(
                current($this->docblock->getTagsByName('return'))
            );
        }

        return true;
    }

    /**
     * Validates whether this element has a docblock.
     *
     * @return bool
     */
    protected function hasDocBlock()
    {
        if ($this->docblock) {
            return true;
        }

        $this->logParserError(
            'ERROR',
            $this instanceof MethodValidator ? 50010 : 50009,
            $this->lineNumber,
            array($this->entityName . '()')
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

        $this->logParserError(
            'ERROR',
            $this instanceof MethodValidator ? 50012 : 50011,
            $this->lineNumber,
            array($this->entityName . '()')
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
        /** @var \phpDocumentor\Reflection\FunctionReflector $source  */
        $source = $this->source;
        $params = $this->docblock->getTagsByName('param');
        $arguments = $source->getArguments();

        foreach (array_values($arguments) as $key => $argument) {
            if (!$this->isArgumentInDocBlock($key, $argument, $params)) {
                continue;
            }

            $this->doesArgumentNameMatchParam($params[$key], $argument);
            $this->doesArgumentTypehintMatchParam($params[$key], $argument);
        }

        foreach ($params as $param) {
            $param_name = $param->getVariableName();
            $this->isDefaultIdeType($param);

            if (isset($arguments[$param_name])) {
                continue;
            }

            $this->logParserError(
                'NOTICE', 50013, $this->lineNumber,
                array($param_name, $this->entityName . '()')
            );
        }
    }

    /**
     * Validates whether the name of the argument is the same as that of the
     * param tag.
     *
     * If the param tag does not contain a name then this method will set it
     * based on the argument.
     *
     * @param \phpDocumentor\Reflection\DocBlock\Tag\ParamTag $param    param to
     *     validate with.
     * @param \phpDocumentor\Reflection\FunctionReflector\ArgumentReflector $argument Argument
     *     to validate against.
     *
     * @return bool whether an issue occurred
     */
    protected function doesArgumentNameMatchParam(
        \phpDocumentor\Reflection\DocBlock\Tag\ParamTag $param,
        \phpDocumentor\Reflection\FunctionReflector\ArgumentReflector $argument
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
            'ERROR', 50014, $this->lineNumber,
            array($argument->getName(), $param_name, $this->entityName . '()')
        );

        return false;
    }

    /**
     * Validates whether an argument is mentioned in the docblock.
     *
     * @param integer                                  $index    The position in
     *     the argument listing.
     * @param \phpDocumentor\Reflection\FunctionReflector\ArgumentReflector $argument The argument
     *     itself.
     * @param \phpDocumentor\Reflection\DocBlock\Tag[] $params   The list of
     *     param tags to validate against.
     *
     * @return bool whether an issue occurred.
     */
    protected function isArgumentInDocBlock(
        $index,
        \phpDocumentor\Reflection\FunctionReflector\ArgumentReflector $argument,
        array $params
    ) {
        if (isset($params[$index])) {
            return true;
        }

        $this->logParserError(
            'ERROR', 50015, $this->lineNumber,
            array($argument->getName(), $this->entityName . '()')
        );
        return false;
    }

    /**
     * Checks the typehint of the argument versus the @param tag.
     *
     * If the argument has no typehint we do not check anything. When multiple
     * type are given then the typehint needs to be one of them.
     *
     * @param \phpDocumentor\Reflection\DocBlock\Tag\ParamTag $param
     * @param \phpDocumentor\Reflection\FunctionReflector\ArgumentReflector $argument
     *
     * @return bool whether an issue occurred
     */
    protected function doesArgumentTypehintMatchParam(
        \phpDocumentor\Reflection\DocBlock\Tag\ParamTag $param,
        \phpDocumentor\Reflection\FunctionReflector\ArgumentReflector $argument
    ) {
        if (!$argument->getType()
            || in_array($argument->getType(), $param->getTypes())
        ) {
            return true;
        } else if ($argument->getType() == 'array'
            && substr($param->getType(), -2) == '[]'
        ) {
            return true;
        }

        $this->logParserError(
            'ERROR', 50016, $this->lineNumber,
            array($argument->getName(), $this->entityName . '()')
        );

        return false;
    }

    /**
     * Checks whether the type of the given tag is not 'type'; which would
     * indicate a non-changed IDE value.
     *
     * @param \phpDocumentor\Reflection\DocBlock\Tag\ParamTag
     *     |\phpDocumentor\Reflection\DocBlock\Tag\ReturnTag $param
     *
     * @return bool whether an issue occurred
     */
    protected function isDefaultIdeType($param)
    {
        if ($param->getType() != 'type') {
            return true;
        }

        $this->logParserError(
            'NOTICE', 50017, $this->lineNumber,
            array('@' . $param->getName(), $this->entityName . '()')
        );
    }
}
