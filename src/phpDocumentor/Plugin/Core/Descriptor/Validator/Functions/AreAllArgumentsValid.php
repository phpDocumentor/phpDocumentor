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

namespace phpDocumentor\Plugin\Core\Descriptor\Validator\Functions;

use phpDocumentor\Descriptor\Validator\Error;
use phpDocumentor\Reflection\BaseReflector;
use phpDocumentor\Reflection\DocBlock\Tag\ParamTag;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\FunctionReflector\ArgumentReflector;
use phpDocumentor\Reflection\FunctionReflector;
use Psr\Log\LogLevel;

/**
 * @todo break this validator up in subvalidators for each Error
 */
class AreAllArgumentsValid
{
    /**
     * Validates whether the given Reflector's arguments match the business rules of phpDocumentor.
     *
     * @param BaseReflector $element
     *
     * @throws \UnexpectedValueException if no DocBlock is associated with the given Reflector.
     *
     * @return Error|null
     */
    public function validate($element)
    {
        $docBlock = $element->getDocBlock();
        if (null === $docBlock) {
            throw new \UnexpectedValueException(
                'A DocBlock should be present (and validated) before this validator can be applied'
            );
        }

        if ($docBlock->hasTag('return')) {
            $returnTag = current($docBlock->getTagsByName('return'));
            if ($returnTag->getType() == 'type') {
                return new Error(LogLevel::WARNING, 'PPC:ERR-50017', $element->getLinenumber());
            }
        }

        return null;
    }

    /**
     * Returns an error if the given Reflector's arguments do not match expectations.
     *
     * @param FunctionReflector $element
     *
     * @return Error|null
     */
    protected function validateArguments($element)
    {
        $params = $element->getDocBlock()->getTagsByName('param');
        $arguments = $element->getArguments();

        foreach (array_values($arguments) as $key => $argument) {
            if (!$this->isArgumentInDocBlock($key, $argument, $element, $params)) {
                continue;
            }

            $result = $this->doesArgumentNameMatchParam($params[$key], $argument, $element);
            if ($result) {
                return $result;
            }

            $result = $this->doesArgumentTypehintMatchParam($params[$key], $argument, $element);
            if ($result) {
                return $result;
            }
        }

        /** @var ParamTag $param */
        foreach ($params as $param) {
            $param_name = $param->getVariableName();

            if (isset($arguments[$param_name])) {
                continue;
            }

            return new Error(
                LogLevel::NOTICE,
                'PPC:ERR-50013',
                $element->getLinenumber(),
                array($param_name, $element->getName())
            );
        }

        return null;
    }

    /**
     * Validates whether an argument is mentioned in the docblock.
     *
     * @param integer           $index    The position in the argument listing.
     * @param ArgumentReflector $argument The argument itself.
     * @param BaseReflector     $element
     * @param Tag[]             $params   The list of param tags to validate against.
     *
     * @return bool whether an issue occurred.
     */
    protected function isArgumentInDocBlock($index, ArgumentReflector $argument, BaseReflector $element, array $params)
    {
        if (isset($params[$index])) {
            return null;
        }

        return new Error(
            LogLevel::ERROR,
            'PPC:ERR-50015',
            $argument->getLinenumber(),
            array($argument->getName(), $element->getName())
        );
    }

    /**
     * Validates whether the name of the argument is the same as that of the
     * param tag.
     *
     * If the param tag does not contain a name then this method will set it
     * based on the argument.
     *
     * @param ParamTag          $param    param to validate with.
     * @param ArgumentReflector $argument Argument to validate against.
     * @param BaseReflector     $element
     *
     * @return Error|null whether an issue occurred
     */
    protected function doesArgumentNameMatchParam(ParamTag $param, ArgumentReflector $argument, BaseReflector $element)
    {
        $param_name = $param->getVariableName();
        if ($param_name == $argument->getName()) {
            return null;
        }

        if ($param_name == '') {
            $param->setVariableName($argument->getName());

            return null;
        }

        return new Error(
            LogLevel::ERROR,
            'PPC:ERR-50014',
            $argument->getLinenumber(),
            array($argument->getName(), $param_name, $element->getName())
        );
    }

    /**
     * Checks the typehint of the argument versus the @param tag.
     *
     * If the argument has no typehint we do not check anything. When multiple
     * type are given then the typehint needs to be one of them.
     *
     * @param ParamTag          $param
     * @param ArgumentReflector $argument
     * @param BaseReflector     $element
     *
     * @return Error|null
     */
    protected function doesArgumentTypehintMatchParam(
        ParamTag $param,
        ArgumentReflector $argument,
        BaseReflector $element
    ) {
        if (!$argument->getType() || in_array($argument->getType(), $param->getTypes())) {
            return null;
        } elseif ($argument->getType() == 'array' && substr($param->getType(), -2) == '[]') {
            return null;
        }

        return new Error(
            LogLevel::ERROR,
            'PPC:ERR-50016',
            $argument->getLinenumber(),
            array($argument->getName(), $element->getName())
        );
    }
}
