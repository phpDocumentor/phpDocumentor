<?php
/**
 * File contains the
 * \phpDocumentor\Plugin\Core\Descriptor\Validator\DeprecatedValidator class.
 *
 * PHP Version 5.3
 *
 * @copyright  2010-2014 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */

//namespace phpDocumentor\Plugin\Core\Descriptor\Validator;
//
///**
// * This class is responsible for validating which tags are deprecated
// * as defined in /src/phpDocumentor/Plugin/Core/plugin.xml
// *
// * @category   phpDocumentor
// * @package    Parser
// * @subpackage DocBlock_Validators
// * @author     Ben Selby <benmatselby@gmail.com>
// * @author     Mike van Riel <mike.vanriel@naenius.com>
// * @copyright  2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
// * @license    http://www.opensource.org/licenses/mit-license.php MIT
// * @link       http://phpdoc.org
// */
//class DeprecatedValidator extends ValidatorAbstract
//{
//    /**
//     * Is the docblock valid based on the rules defined in plugin.xml
//     *
//     * <options>
//     *   <option name="deprecated">
//     *      <tag name="deprecated" />
//     *      <tag name="access" />
//     *   </option>
//     *   <option name="required">
//     *     <tag name="package">
//     *       <element>phpDocumentor\Reflection\FileReflector</element>
//     *       <element>phpDocumentor\Reflection\ClassReflector</element>
//     *     </tag>
//     *     <tag name="subpackage">
//     *       <element>phpDocumentor\Reflection\FileReflector</element>
//     *       <element>phpDocumentor\Reflection\ClassReflector</element>
//     *     </tag>
//     *   </option>
//     * </options>
//     *
//     * @see ValidatorAbstract::isValid()
//     *
//     * @return boolean
//     */
//    public function isValid()
//    {
//        $docType = get_class($this->source);
//        if (isset($this->options['deprecated'][$docType])) {
//            $this->validateTags($docType);
//        } elseif (isset($this->options['deprecated']['__ALL__'])) {
//            $this->validateTags('__ALL__');
//        }
//    }
//
//    /**
//     * Validate the tags based on the type of docblock being
//     * parsed etc
//     *
//     * @param string $key Access key to $this->options['required']
//     *
//     * @return void
//     */
//    protected function validateTags($key)
//    {
//        foreach ($this->options['deprecated'][$key] as $tag) {
//            if (count($this->docblock->getTagsByName($tag)) < 1) {
//                continue;
//            }
//            $this->logParserError('CRITICAL', 'PPC:ERR-50006', $this->lineNumber, array($tag, $this->entityName));
//        }
//    }
//}
