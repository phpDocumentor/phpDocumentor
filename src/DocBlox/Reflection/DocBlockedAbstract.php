<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category  DocBlox
 * @package   Reflection
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://docblox-project.org
 */

/**
 * Abstract base class for all Reflection entities which have a docblock.
 *
 * @category DocBlox
 * @package  Reflection
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://docblox-project.org
 */
abstract class DocBlox_Reflection_DocBlockedAbstract extends DocBlox_Reflection_Abstract
{
    protected $default_package_name = 'Default';

    /** @var DocBlox_Reflection_DocBlock|null */
    protected $doc_block = null;

    /**
     * Any generic information that needs to be retrieved is the docblock itself.
     *
     * @param DocBlox_Reflection_TokenIterator $tokens Tokens to process
     *
     * @return void
     */
    protected function processGenericInformation(DocBlox_Reflection_TokenIterator $tokens)
    {
        $this->doc_block = $this->findDocBlock($tokens);
    }

    /**
     * Returns the DocBlock reflection object.
     *
     * @return DocBlox_Reflection_Docblock|null
     */
    public function getDocBlock()
    {
        return $this->doc_block;
    }

    /**
     * Returns the first docblock preceding the active token within 10 tokens.
     *
     * Please note that the iterator cursor does not change with to this method.
     *
     * @param DocBlox_Reflection_TokenIterator $tokens Tokens to process
     *
     * @return DocBlox_Reflection_DocBlock|null
     */
    protected function findDocBlock(DocBlox_Reflection_TokenIterator $tokens)
    {
        $result = null;
        $docblock = $tokens->findPreviousByType(T_DOC_COMMENT, 10, array('{', '}', ';'));
        try {
            $result = $docblock ? new DocBlox_Reflection_DocBlock($docblock->content) : null;
            if ($result) {
                // attach line number to class, the DocBlox_Reflection_DocBlock does not know the number
                $result->line_number = $docblock->line_number;
            }
        } catch (Exception $e) {
            $this->log($e->getMessage(), Zend_Log::CRIT);
        }

        $this->validateDocBlock($this->name, $docblock ? $docblock->line_number : 0, $result);

        // if the object has no DocBlock _and_ is not a Closure; throw a warning
        $type = substr(get_class($this), strrpos(get_class($this), '_') + 1);
        if (!$result && (($type !== 'Function') && ($this->getName() !== 'Closure'))) {
            $this->logParserError('ERROR', 'No DocBlock was found for ' . $type
                    . ' ' . $this->getName(), $this->getLineNumber());
        }

        return $result;
    }

    /**
     * Tries to expand a type to it's full namespaced equivalent.
     *
     * @param string $type Type to expand into full namespaced equivalent
     *
     * @return string
     */
    public function expandType($type)
    {
        if ($type === null) {
            return null;
        }

        $non_objects = array(
            'string', 'int', 'integer', 'bool', 'boolean', 'float', 'double',
            'object', 'mixed', 'array', 'resource', 'void', 'null', 'callback',
            'false', 'true'
        );
        $namespace = $this->getNamespace() == 'default' ? '' : $this->getNamespace().'\\';

        $type = explode('|', $type);
        foreach ($type as &$item) {
            $item = trim($item);

            // add support for array notation
            $is_array = false;
            if (substr($item, -2) == '[]') {
                $item = substr($item, 0, -2);
                $is_array = true;
            }

            if ((substr($item, 0, 1) != '\\') && (!in_array(strtolower($item), $non_objects))) {
                $type_parts = explode('\\', $item);

                // if the first part is the keyword 'namespace', replace it with the current namespace
                if ($type_parts[0] == 'namespace') {
                    $type_parts[0] = $this->getNamespace();
                    $item = implode('\\', $type_parts);
                }

                // if the first segment is an alias; replace with full name
                if (isset($this->namespace_aliases[$type_parts[0]])) {
                    $type_parts[0] = $this->namespace_aliases[$type_parts[0]];

                    $item = implode('\\', $type_parts);
                } elseif (count($type_parts) == 1) {
                    // prefix the item with the namespace if there is only one part and no alias
                    $item = $namespace . $item;
                }
            }

            // re-add the array notation markers
            if ($is_array) {
                $item .= '[]';
            }

            // full paths always start with a slash
            if (isset($item[0]) && ($item[0] !== '\\') && (!in_array(strtolower($item), $non_objects)))
            {
                $item = '\\' . $item;
            }
        }

        return implode('|', $type);
    }

    /**
     * Adds the DocBlock XML definition to the given SimpleXMLElement.
     *
     * @param SimpleXMLElement $xml SimpleXMLElement to be appended to
     *
     * @return void
     */
    protected function addDocblockToSimpleXmlElement(SimpleXMLElement $xml)
    {
        $package = '';
        $subpackage = '';

        if ($this->getDocBlock()) {
            if (!isset($xml->docblock)) {
                $xml->addChild('docblock');
            }

            $xml->docblock->description          = $this->getDocBlock()->getShortDescription();
            $xml->docblock->{'long-description'} = $this->getDocBlock()->getLongDescription()->getFormattedContents();


            /** @var DocBlox_Reflection_Docblock_Tag $tag */
            foreach ($this->getDocBlock()->getTags() as $tag) {
                $tag_object = $xml->docblock->addChild('tag');

                $this->dispatch(
                    'reflection.docblock.tag.pre-export',
                    array(
                        self::$event_dispatcher,
                        $this,
                        $tag_object,
                        $tag
                    )
                );

                DocBlox_Parser_DocBlock_Tag_Definition::create($this->getNamespace(), $this->namespace_aliases, $tag_object, $tag);

                // custom attached member variable, see line 51
                if (isset($this->getDocBlock()->line_number)) {
                    $tag_object['line'] = $this->getDocBlock()->line_number;
                }

                if ($tag->getName() == 'package') {
                    $package = $tag->getDescription();
                }

                if ($tag->getName() == 'subpackage') {
                    $subpackage = $tag->getDescription();
                }
            }
        }

        // create a new 'meta-package' shaped like a namespace
        $xml['package'] = str_replace(
            array('.', '_'),
            '\\',
            $package . ($subpackage ? '\\' . $subpackage : '')
        );

        if ((string)$xml['package'] == '') {
            $xml['package'] = $this->getDefaultPackageName();
        }
    }

    /**
     * Validate the docblock
     *
     * @param string                           $name       Name of entity to be validated
     * @param int                              $lineNumber The line number for the docblock
     * @param DocBlox_Reflection_DocBlock|null $docblock   Docbloc
     *
     * @return boolean
     */
    protected function validateDocBlock($name, $lineNumber, $docblock)
    {
        $valid = true;
        $class = get_class($this);
        $part = substr($class, strrpos($class, '_') + 1);

        if (@class_exists('DocBlox_Parser_DocBlock_Validator_'.$part)) {
            $validatorType = 'DocBlox_Parser_DocBlock_Validator_' . $part;
            $validator = new $validatorType($name, $lineNumber, $docblock);
            $valid = $validator->isValid();
        }
        return $valid;
    }

    /**
     * Sets the name of the Default package.
     *
     * @param string $default_package_name
     *
     * @return void
     */
    public function setDefaultPackageName($default_package_name)
    {
        $this->default_package_name = $default_package_name;
    }

    /**
     * Returns the name of the default package.
     *
     * @return string
     */
    public function getDefaultPackageName()
    {
        return $this->default_package_name;
    }
}
