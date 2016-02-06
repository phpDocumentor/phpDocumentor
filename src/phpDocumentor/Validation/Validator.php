<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Validation;


use phpDocumentor\Reflection\Php\File;

final class Validator
{
    /**
     * @var RuleSet
     */
    private $ruleSet;

    /**
     * Validator constructor.
     */
    public function __construct(RuleSet $ruleSet)
    {
        $this->ruleSet = $ruleSet;
    }

    public function validate(File $file)
    {
        $result = new Result();
        $this->doValidate($file, $result);

        foreach ($file->getConstants() as $constant) {
            $this->doValidate($constant, $result);
        }

        foreach ($file->getFunctions() as $function) {
            $this->doValidate($function, $result);
        }

        foreach ($file->getInterfaces() as $interface) {
            $this->doValidate($interface, $result);

            foreach ($interface->getMethods() as $method) {
                $this->doValidate($method, $result);
            }

            foreach ($interface->getConstants() as $constant) {
                $this->doValidate($constant, $result);
            }
        }

        foreach ($file->getTraits() as $trait) {
            $this->doValidate($trait);
            foreach ($trait->getMethods() as $method) {
                $this->doValidate($method, $result);
            }

            foreach ($trait->getProperties() as $property) {
                $this->doValidate($property, $result);
            }
        }

        foreach ($file->getClasses() as $class) {
            $this->doValidate($class, $result);
            foreach ($class->getMethods() as $method) {
                $this->doValidate($method, $result);
            }

            foreach ($class->getProperties() as $property) {
                $this->doValidate($property, $result);
            }

            foreach ($class->getConstants() as $constant) {
                $this->doValidate($constant, $result);
            }
        }

        return $result;
    }

    /**
     * @param $element
     * @param Result $result
     */
    private function doValidate($element, Result $result)
    {
        foreach ($this->ruleSet->getRules() as $rule) {
            $rule->validate($element, $result);
        }
    }
}
