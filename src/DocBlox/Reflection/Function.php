<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category  DocBlox
 * @package   Static_Reflection
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://docblox-project.org
 */

/**
 * Reflection class for a function declaration.
 *
 * @category DocBlox
 * @package  Static_Reflection
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://docblox-project.org
 */
class DocBlox_Reflection_Function extends DocBlox_Reflection_BracesAbstract
{
    /** @var string identifier for the 'type' value of FUNCTION */
    const TYPE_FUNCTION = 'function';

    /** @var string identifier for the 'type' value of CLOSURE */
    const TYPE_CLOSURE = 'closure';

    /** @var int Index of the first token in the argument list*/
    protected $arguments_token_start = 0;

    /** @var int Index of the last token in the argument list*/
    protected $arguments_token_end = 0;

    /** @var DocBlox_Reflection_Argument[] contains all arguments */
    protected $arguments = array();

    /** @var string Whether this is a 'function' or 'closure' */
    protected $type = self::TYPE_FUNCTION;

    /**
     * Retrieves the generic information.
     *
     * Finds out which name and arguments this function has on top of the information found using the
     * DocBlox_Reflection_BracesAbstract parent method.
     *
     * @param DocBlox_Token_Iterator $tokens
     *
     * @see DocBlox_ReflectionBracesAbstract::processGenericInformation
     *
     * @return void
     */
    protected function processGenericInformation(DocBlox_Token_Iterator $tokens) {
        $this->setName($this->findName($tokens));

        $this->resetTimer();
        parent::processGenericInformation($tokens);

        list($start_index, $end_index) = $tokens->getTokenIdsOfParenthesisPair();
        $this->arguments_token_start = $start_index;
        $this->arguments_token_end = $end_index;
        $this->debugTimer('>> Determined argument range token ids');
    }

    /**
     * Extracts the arguments from this function.
     *
     * @param DocBlox_Token_Iterator $tokens
     *
     * @return void
     */
    public function processVariable(DocBlox_Token_Iterator $tokens) {
        // is the variable occurs within arguments parenthesis then it is an argument
        if (($tokens->key() > $this->arguments_token_start) && ($tokens->key() < $this->arguments_token_end)) {
            $this->resetTimer('variable');

            $argument = new DocBlox_Reflection_Argument();
            $argument->parseTokenizer($tokens);
            $this->arguments[$argument->getName()] = $argument;

            if ($this->getDocBlock()) {
                /** @var DocBlox_Reflection_DocBlock_Tag $params  */
                $params = $this->getDocBlock()->getTagsByName('param');
                if (!isset($params[count($this->arguments) - 1])) {
                    $this->log(
                        'Argument ' . $argument->getName() . ' is missing from the function Docblock in file '
                                . $tokens->getFilename() . ' at line ' . $argument->getLineNumber(),
                        DocBlox_Core_Log::WARN
                    );
                } else
                {
                    $param_name = $params[count($this->arguments) - 1]->getVariableName();
                    if ($param_name != $argument->getName()) {
                        if ($param_name == '') {
                            $params[count($this->arguments) - 1]->setVariableName($argument->getName());
                        }
                        else
                        {
                            $this->log(
                                'Name of argument ' . $argument->getName() . ' does not match with function Docblock in file '
                                        . $tokens->getFilename() . ' at line ' . $argument->getLineNumber(),
                                DocBlox_Core_Log::WARN
                            );
                        }
                    }
                }
            }

            $this->debugTimer('>> Processed argument ' . $argument->getName(), 'variable');
        }
    }

    /**
     * Finds the name of this function starting from the T_FUNCTION token.
     *
     * If a function has no name it is probably a Closure and will have the name Closure.
     *
     * @param DocBlox_Token_Iterator $tokens
     *
     * @return string
     */
    protected function findName(DocBlox_Token_Iterator $tokens) {
        $name = $tokens->findNextByType(T_STRING, 5, array('{', ';'));

        $this->setType($name ? self::TYPE_FUNCTION : self::TYPE_CLOSURE);

        return $name ? $name->getContent() : 'Closure';
    }

    /**
     * Sets whether this is a function or closure.
     *
     * @param string $type
     *
     * @return void
     */
    public function setType($type) {
        if (!in_array($type, array(self::TYPE_CLOSURE, self::TYPE_FUNCTION))) {
            throw new InvalidArgumentException(
                'Expected type of function to either match "' . self::TYPE_FUNCTION . '" or "' . self::TYPE_CLOSURE
                        . '", received: ' . $type
            );
        }

        $this->type = $type;
    }

    /**
     * Returns whether this is a function or closure.
     *
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Returns the XML representation of this object or false if an error occurred.
     *
     * @return string|boolean
     */
    public function __toXml() {
        $xml = new SimpleXMLElement('<function></function>');
        $xml['namespace'] = $this->getNamespace();
        $xml['line'] = $this->getLineNumber();
        $xml->name = $this->getName();
        $xml->type = $this->getType();
        $this->addDocblockToSimpleXmlElement($xml);

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($xml->asXML());

        foreach ($this->arguments as $argument)
        {
            $this->mergeXmlToDomDocument($dom, $argument->__toXml());
        }

        return trim($dom->saveXML());
    }

}