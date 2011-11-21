<?php
/**
 * File contains the DocBlox_Plugin_Core_Parser_DocBlock_Validator_Required class
 *
 * PHP Version 5
 *
 * @category   DocBlox
 * @package    Parser
 * @subpackage DocBlock_Validators
 * @author     Ben Selby <benmatselby@gmail.com>
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
/**
 * This class is responsible for validating which tags are required
 * as defined in Docblox/src/DocBlox/Plugin/Core/plugin.xml
 *
 * @category   DocBlox
 * @package    Parser
 * @subpackage DocBlock_Validators
 * @author     Ben Selby <benmatselby@gmail.com>
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Plugin_Core_Parser_DocBlock_Validator_Required
    extends DocBlox_Plugin_Core_Parser_DocBlock_Validator_Abstract
{
    /**
     * Is the docblock valid based on the rules defined in plugin.xml
     *
     * <options>
     *   <option name="deprecated">
     *      <tag name="deprecated" />
     *      <tag name="access" />
     *   </option>
     *   <option name="required">
     *     <tag name="package">
     *       <element>DocBlox_Reflection_File</element>
     *       <element>DocBlox_Reflection_Class</element>
     *     </tag>
     *     <tag name="subpackage">
     *       <element>DocBlox_Reflection_File</element>
     *       <element>DocBlox_Reflection_Class</element>
     *     </tag>
     *   </option>
     * </options>
     *
     * @see DocBlox_Plugin_Core_Parser_DocBlock_Validator_Abstract::isValid()
     *
     * @return void
     */
    public function isValid()
    {
        $docType = get_class($this->source);
        if (isset($this->options['required'][$docType])) {
            $this->validateTags($docType);
        } else if (isset($this->options['required']['__ALL__'])) {
            $this->validateTags('__ALL__');
        }
    }

    /**
     * Validate the tags based on the type of docblock being
     * parsed etc
     *
     * @param string $key Access key to $this->options['required']
     *
     * @return void
     */
    protected function validateTags($key)
    {
        foreach ($this->options['required'][$key] as $tag) {
            if (count($this->docblock->getTagsByName($tag)) == 0) {

                $this->logParserError(
                    'CRITICAL', 'Not found required tag "' . $tag .'" in '
                    . $this->entityName,
                    $this->lineNumber
                );
            }
        }
    }
}