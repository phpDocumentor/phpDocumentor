<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category  phpDocumentor
 * @package   Reflection
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

/**
 * Abstract base class for all Reflection entities which have a docblock.
 *
 * @category phpDocumentor
 * @package  Reflection
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://phpdoc.org
 */
abstract class phpDocumentor_Reflection_DocBlockedAbstract
    extends phpDocumentor_Reflection_Abstract
{
    protected $default_package_name = 'Default';

    /** @var phpDocumentor_Reflection_DocBlock|null */
    protected $doc_block = null;

    /**
     * Any generic information that needs to be retrieved is the docblock itself.
     *
     * @param phpDocumentor_Reflection_TokenIterator $tokens Tokens to process
     *
     * @return void
     */
    protected function processGenericInformation(
        phpDocumentor_Reflection_TokenIterator $tokens
    ) {
        $this->doc_block = $this->findDocBlock($tokens);
    }

    /**
     * Returns the DocBlock reflection object.
     *
     * @return phpDocumentor_Reflection_Docblock|null
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
     * @param phpDocumentor_Reflection_TokenIterator $tokens Tokens to process
     *
     * @return phpDocumentor_Reflection_DocBlock|null
     */
    protected function findDocBlock(phpDocumentor_Reflection_TokenIterator $tokens)
    {
        $result = null;
        $docblock = $tokens->findPreviousByType(
            T_DOC_COMMENT, 10, array('{', '}', ';')
        );
        try {
            $result = $docblock
                ? new phpDocumentor_Reflection_DocBlock($docblock->content)
                : null;

            if ($result) {
                // attach line number to class, the phpDocumentor_Reflection_DocBlock
                // does not know the number
                $result->line_number = $docblock->line_number;
            }
        } catch (Exception $e) {
            $this->log($e->getMessage(), Zend_Log::CRIT);
            return $result;
        }

        return $result;
    }

    /**
     * Tries to expand a type to it's full namespaced equivalent.
     *
     * @param string $type               Type to expand into full namespaced
     *  equivalent.
     * @param bool   $ignore_non_objects whether to ignore reserved words, when
     *  false it will not expand a set of keywords.
     *
     * @return string
     */
    public function expandType($type, $ignore_non_objects = false)
    {
        if ($type === null) {
            return null;
        }

        $non_objects = array();
        if (!$ignore_non_objects) {
            $non_objects = array(
                'string', 'int', 'integer', 'bool', 'boolean', 'float', 'double',
                'object', 'mixed', 'array', 'resource', 'void', 'null',
                'callback', 'false', 'true'
            );
        }
        $namespace = $this->getNamespace() == 'default'
            ? '' : $this->getNamespace().'\\';

        $type = explode('|', $type);
        foreach ($type as &$item) {
            $item = trim($item);

            // add support for array notation
            $is_array = false;
            if (substr($item, -2) == '[]') {
                $item = substr($item, 0, -2);
                $is_array = true;
            }

            if ((substr($item, 0, 1) != '\\')
                && (!in_array(strtolower($item), $non_objects))
            ) {
                $type_parts = explode('\\', $item);

                // if the first part is the keyword 'namespace', replace it
                // with the current namespace
                if ($type_parts[0] == 'namespace') {
                    $type_parts[0] = $this->getNamespace();
                    $item = implode('\\', $type_parts);
                }

                // if the first segment is an alias; replace with full name
                if (isset($this->namespace_aliases[$type_parts[0]])) {
                    $type_parts[0] = $this->namespace_aliases[$type_parts[0]];

                    $item = implode('\\', $type_parts);
                } elseif (count($type_parts) == 1) {
                    // prefix the item with the namespace if there is only one
                    // part and no alias
                    $item = $namespace . $item;
                }
            }

            // re-add the array notation markers
            if ($is_array) {
                $item .= '[]';
            }

            // full paths always start with a slash
            if (isset($item[0]) && ($item[0] !== '\\')
                && (!in_array(strtolower($item), $non_objects))
            ) {
                $item = '\\' . $item;
            }
        }

        return implode('|', $type);
    }

    /**
     * Sets the name of the Default package.
     *
     * @param string $default_package_name The name that needs to be adopted by
     *     elements without package tags.
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

    /**
     * Override method to add a validation point for the docblock where it
     * has access to all processed tokens.
     *
     * @param phpDocumentor_Reflection_TokenIterator $tokens
     */
    public function parseTokenizer(phpDocumentor_Reflection_TokenIterator $tokens)
    {
        parent::parseTokenizer($tokens);

        $this->dispatch(
            'reflection.docblock-extraction.post',
            array('docblock' => $this->getDocBlock())
        );
    }

}
