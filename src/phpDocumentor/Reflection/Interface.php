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

if (!defined('T_NS_SEPARATOR')) {
    /** @var int This constant is PHP 5.3+, but is necessary for correct parsing */
    define('T_NS_SEPARATOR', 380);
}

/**
 * Parses an interface definition.
 *
 * @category phpDocumentor
 * @package  Reflection
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://phpdoc.org
 */
class phpDocumentor_Reflection_Interface extends phpDocumentor_Reflection_BracesAbstract
{
    /** @var bool Whether this interface extends another */
    protected $extends = false;

    /** @var string|null $extends Where this interface extends from. */
    protected $extendsFrom = null;

    /** @var bool Whether this interface implements another. */
    protected $implements = false;

    /** @var string[] Which interfaces this Interface implements. */
    protected $interfaces = array();

    /**
     * @var phpDocumentor_Reflection_Constant Which constants are present in
     *     this interface
     */
    protected $constants = array();

    /**
     * @var phpDocumentor_Reflection_Property Which properties are present in
     *     this interface
     */
    protected $properties = array();

    /**
     * @var phpDocumentor_Reflection_Method Which methods are present in this interface
     */
    protected $methods = array();

    /**
     * Retrieve the name of the class starting from the T_CLASS token.
     *
     * @param phpDocumentor_Reflection_TokenIterator $tokens Tokens to interpret with
     *     the pointer at the token to be processed.
     *
     * @return string
     */
    protected function extractClassName(phpDocumentor_Reflection_TokenIterator $tokens)
    {
        // a class name can be a combination of a T_NAMESPACE and T_STRING
        $name = '';
        $limits = array(';', '{', ',');

        /** @var phpDocumentor_Token $token */
        while ($token = $tokens->next()) {
            if (in_array($token->content, $limits) || ((strlen(trim($name)) >= 1)
                && ($token->type == T_WHITESPACE))
            ) {
                $tokens->previous();
                break;
            }

            $name .= $token->content;
        }

        return trim($name);
    }

    /**
     * Extract and store the meta data surrounding a class / interface.
     *
     * @param phpDocumentor_Reflection_TokenIterator $tokens Tokens to interpret with
     *     the pointer at the token to be processed.
     *
     * @return void
     */
    protected function processGenericInformation(
        phpDocumentor_Reflection_TokenIterator $tokens
    ) {
        // retrieve generic information about the class
        $this->setName($this->extractClassName($tokens));
        $this->doc_block = $this->findDocBlock($tokens);

        // parse a EXTENDS section
        $extends = $tokens->gotoNextByType(T_EXTENDS, 5, array('{'));
        $this->extends = ($extends) ? true : false;
        $this->extendsFrom = ($extends) ? $this->extractClassName($tokens) : null;

        // Parse an eventual implements section: implements _always_
        // follows extends
        $implements = $tokens->gotoNextByType(T_IMPLEMENTS, 5, array('{'));
        $interfaces = array();
        if ($implements) {
            do {
                $interfaces[] = $this->extractClassName($tokens);
            } while ($tokens->next()->content == ',');
        }

        $this->implements = ($implements) ? true : false;
        $this->interfaces = $interfaces;
    }

    /**
     * Processes a T_CONST token found inside a class / interface definition.
     *
     * @param phpDocumentor_Reflection_TokenIterator $tokens Tokens to interpret with
     *     the pointer at the token to be processed.
     *
     * @return void
     */
    protected function processConst(phpDocumentor_Reflection_TokenIterator $tokens)
    {
        $constant = new phpDocumentor_Reflection_Constant();
        $constant->parseTokenizer($tokens);
        $constant->setNamespace($this->getNamespace());
        $constant->setNamespaceAliases($this->getNamespaceAliases());
        $constant->setDefaultPackageName($this->getDefaultPackageName());
        $this->constants[] = $constant;
    }

    /**
     * Processes a T_VARIABLE token found inside a class / interface definition.
     *
     * @param phpDocumentor_Reflection_TokenIterator $tokens Tokens to interpret with
     *     the pointer at the token to be processed.
     *
     * @return void
     */
    protected function processVariable($tokens)
    {
        $property = new phpDocumentor_Reflection_Property();
        $property->parseTokenizer($tokens);
        $property->setNamespace($this->getNamespace());
        $property->setNamespaceAliases($this->getNamespaceAliases());
        $property->setDefaultPackageName($this->getDefaultPackageName());
        $this->properties[] = $property;
    }

    /**
     * Processes a T_FUNCTION token found inside a class / interface definition.
     *
     * @param phpDocumentor_Reflection_TokenIterator $tokens Tokens to interpret with
     *     the pointer at the token to be processed.
     *
     * @return void
     */
    protected function processFunction($tokens)
    {
        $method = new phpDocumentor_Reflection_Method();
        $method->parseTokenizer($tokens);
        $method->setNamespace($this->getNamespace());
        $method->setNamespaceAliases($this->getNamespaceAliases());
        $method->setDefaultPackageName($this->getDefaultPackageName());
        $this->methods[$method->getName()] = $method;
    }

    /**
     * Returns the name of the superclass.
     *
     * @return string|null
     */
    public function getParentClass()
    {
        return $this->extends ? $this->extendsFrom : null;
    }

    /**
     * Returns the names of the implemented interfaces.
     *
     * @return string[]
     */
    public function getParentInterfaces()
    {
        return $this->interfaces;
    }

    /**
     * Returns an array of constant objects.
     *
     * @return phpDocumentor_Reflection_Constant[]
     */
    public function getConstants()
    {
        return $this->constants;
    }

    /**
     * Returns an array of property objects.
     *
     * @return phpDocumentor_Reflection_Property[]
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Returns an array of method objects.
     *
     * @return phpDocumentor_Reflection_Method[]
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * Returns the method with the given name or null if none is found.
     *
     * @param string $name Name of the method to return.
     *
     * @return phpDocumentor_Reflection_Method[]|null
     */
    public function getMethod($name)
    {
        return isset($this->methods[$name]) ? $this->methods[$name] : null;
    }

}