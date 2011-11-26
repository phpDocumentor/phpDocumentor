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
 * Provides the basic functionality for every static reflection class.
 *
 * @category DocBlox
 * @package  Reflection
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://docblox-project.org
 */
abstract class DocBlox_Reflection_Abstract extends DocBlox_Core_Abstract
{
    /**
     * Stores the method name of the processing method for a token.
     *
     * The generation of method names may be a performance costly task and is
     * quite often executed.
     * As such we cache the method names which are coming from tokens here in
     * this array.
     *
     * @var string[]
     */
    private static $_token_method_cache = array();

    /**
     * Stores the name for this Reflection object.
     *
     * @var string
     */
    protected $name = 'Unknown';

    /**
     * Stores the start position by token index.
     *
     * @var int
     */
    protected $token_start = 0;

    /**
     * Stores the end position by token index.
     *
     * @var int
     */
    protected $token_end = 0;

    /**
     * Stores the line where the initial token was found.
     *
     * @var int
     */
    protected $line_start = 0;

    /**
     * Stores the name of the namespace to which this belongs.
     *
     * @var string
     */
    protected $namespace = 'default';

    /**
     * Stores the aliases and full names of any defined namespace alias (T_USE).
     *
     * @var string[]
     */
    protected $namespace_aliases = array();

    /** @var string The path of the file. */
    protected $filename = '';

    /**
     * The event dispatcher object, may be null to not dispatch events.
     *
     * @var sfEventDispatcher|null
     */
    public static $event_dispatcher = null;

    /**
     * Dispatches an event to the Event Dispatcher.
     *
     * This method tries to dispatch an event; if no Event Dispatcher has been
     * set than this method will explicitly not fail and return null.
     *
     * By not failing we make the Event Dispatcher optional and is it easier
     * for people to re-use this component in their own application.
     *
     * @param string   $name      Name of the event to dispatch.
     * @param string[] $arguments Arguments for this event.
     *
     * @throws DocBlox_Parser_Exception if there is a dispatcher but it is not
     *  of type sfEventDispatcher
     *
     * @return mixed|null
     */
    public function dispatch($name, $arguments)
    {
        if (!self::$event_dispatcher) {
            return null;
        }

        if (!self::$event_dispatcher instanceof sfEventDispatcher) {
            throw new DocBlox_Parser_Exception(
                'Expected the event dispatcher to be an instance of '
                . 'sfEventDispatcher'
            );
        }

        $event = self::$event_dispatcher->notify(
            new sfEvent($this, $name, $arguments)
        );

        return $event
                ? $event->getReturnValue()
                : null;
    }

    /**
     * Dispatches a logging request.
     *
     * @param string $message  The message to log.
     * @param int    $priority The logging priority, the lower,
     *  the more important. Ranges from 1 to 7
     *
     * @return void
     */
    public function log($message, $priority = 6)
    {
        $this->dispatch(
            'system.log',
            array(
                 'message' => $message,
                 'priority' => $priority
            )
        );
    }

    /**
     * Dispatches a logging request.
     *
     * @param int    $type    The logging priority, the lower,
     *  the more important. Ranges from 1 to 7
     * @param string $message The message to log.
     * @param int    $line    Line number where error occurs.
     *
     * @return void
     */
    public function logParserError($type, $message, $line)
    {
        $this->dispatch(
            'parser.log',
            array(
                 'type' => $type,
                 'message' => $message,
                 'line' => $line
            )
        );
    }

    /**
     * Dispatches a logging request to log a debug message.
     *
     * @param string $message The message to log.
     *
     * @return void
     */
    public function debug($message)
    {
        $this->dispatch(
            'system.debug',
            array('message' => $message)
        );
    }

    /**
     * Main function which reads the token iterator and parses the current token.
     *
     * @param DocBlox_Reflection_TokenIterator $tokens The iterator with tokens.
     *
     * @return void
     */
    public function parseTokenizer(DocBlox_Reflection_TokenIterator $tokens)
    {
        if (!$tokens->current()) {
            $this->log('>> No contents found to parse');
            return;
        }

        $this->debug('== Parsing token ' . $tokens->current()->getName());
        $this->line_start = $tokens->current()->getLineNumber();

        // retrieve generic information about the class
        $this->processGenericInformation($tokens);

        list($start, $end) = $this->processTokens($tokens);
        $this->token_start = $start;
        $this->token_end = $end;

        $this->debug(
            '== Determined token index range to be ' . $start . ' => ' . $end
        );

        $this->debugTimer('>> Processed all tokens');
    }

    /**
     * Processes the meta-data of the 'main' token.
     *
     * Example: for the DocBlox_Reflection_Function class this would be the
     * name, parameters, etc.
     *
     * @param DocBlox_Reflection_TokenIterator $tokens The iterator with tokens.
     *
     * @return void
     */
    abstract protected function processGenericInformation(
        DocBlox_Reflection_TokenIterator $tokens
    );

    /**
     * Scans all tokens within the scope of the current token and invokes the
     * process* methods.
     *
     * This is a base class which may be overridden in sub-classes to scan the
     * scope of the current token (i.e. the method body in case of the method)
     *
     * @param DocBlox_Reflection_TokenIterator $tokens iterator with the current
     *     position
     *
     * @return int[] Start and End token id
     */
    protected function processTokens(DocBlox_Reflection_TokenIterator $tokens)
    {
        return array($tokens->key(), $tokens->key());
    }

    /**
     * Processes the current token and invokes the correct process* method.
     *
     * Tokens are automatically parsed by invoking a process* method (i.e.
     * processFunction for a T_FUNCTION).
     * If a method, which conforms to the standard above, does not exist the
     * token is ignored.
     *
     * @param DocBlox_Reflection_Token         $token  The specific token which
     *     needs processing.
     * @param DocBlox_Reflection_TokenIterator $tokens The iterator with tokens.
     *
     * @return void
     */
    protected function processToken(
        DocBlox_Reflection_Token $token,
        DocBlox_Reflection_TokenIterator $tokens
    ) {
        static $token_method_exists_cache = array();

        // cache method name; I expect to find this a lot
        $token_id = $token->type;
        if (!isset(self::$_token_method_cache[$token_id])) {
            // convert 'T_MY_TOKEN' token name to 'MY TOKEN'
            $token_pretty_name = substr(
                str_replace('_', ' ', token_name($token_id)),
                2
            );

            // convert 'MY TOKEN' to 'MyToken'
            $token_pretty_name = str_replace(
                ' ',
                '',
                ucwords(strtolower($token_pretty_name))
            );

            // remember the 'processMyToken' method name
            self::$_token_method_cache[$token_id] = 'process'
                . $token_pretty_name;
        }

        // cache the method_exists calls to speed up processing
        $method_name = self::$_token_method_cache[$token_id];
        if (!isset($token_method_exists_cache[$method_name])) {
            $token_method_exists_cache[$method_name] = method_exists(
                $this, $method_name
            );
        }

        // if method exists; parse the token
        if ($token_method_exists_cache[$method_name] === true) {
            $this->$method_name($tokens);
        }
    }

    /**
     * Find the Type for this object.
     *
     * Please note that the iterator cursor does not change due to this method
     *
     * @param DocBlox_Reflection_TokenIterator $tokens The iterator with tokens.
     *
     * @return string|null
     */
    protected function findType(DocBlox_Reflection_TokenIterator $tokens)
    {
        // first see if there is a string at most 5 characters back
        $type = $tokens->findPreviousByType(T_STRING, 5, array(',', '('));

        // if none found, check if there is an array at most 5 places back
        if (!$type) {
            $type = $tokens->findPreviousByType(T_ARRAY, 5, array(',', '('));
        }

        // if anything is found, return the content
        return $type ? $type->content : null;
    }

    /**
     * Find the Default value for this object.
     *
     * Usually used with variables or arguments.
     * Please note that the iterator cursor does not change due to this method
     *
     * @param DocBlox_Reflection_TokenIterator $tokens The iterator with tokens.
     *
     * @return string|null
     */
    protected function findDefault(DocBlox_Reflection_TokenIterator $tokens)
    {
        $result = '';
        $index = $tokens->key();
        $level = 0;

        /** @var DocBlox_Reflection_Token $token  */
        $token = $tokens->next();

        // only start including the text once we have passed the =
        $include = false;

        // while we have not reached the EOF, and we have not a literal ',',
        // ')' (not nested) or ';' continue gathering elements.
        while ($token
           && !(!$token->type
                && ((($token->content == ')') && ($level == 0))
                    || ($token->content == ';')
                    || ($token->content == ',')
                )
            )
        ) {
            // only include if the = has passed
            if ($include) {
                if ($token->content == '(') {
                    $level++;
                }
                if ($token->content == ')') {
                    $level--;
                }
                $result .= $token->content;
            }

            if (($token->type === null) && ($token->content == '=')) {
                $include = true;
            }

            $token = $tokens->next();
        }
        ;

        $tokens->seek($index);
        return trim($result);
    }

    /**
     * Determine whether this token has the abstract keyword.
     *
     * Please note that the iterator cursor does not change due to this method
     *
     * @param DocBlox_Reflection_TokenIterator $tokens The iterator with tokens.
     *
     * @return DocBlox_Reflection_Token|null
     */
    protected function findAbstract(DocBlox_Reflection_TokenIterator $tokens)
    {
        return $tokens->findPreviousByType(T_ABSTRACT, 5, array('}'));
    }

    /**
     * Determine whether this token has the final keyword.
     *
     * Please note that the iterator cursor does not change due to this method
     *
     * @param DocBlox_Reflection_TokenIterator $tokens The iterator with tokens.
     *
     * @return DocBlox_Reflection_Token|null
     */
    protected function findFinal(DocBlox_Reflection_TokenIterator $tokens)
    {
        return $tokens->findPreviousByType(T_FINAL, 5, array('}'));
    }

    /**
     * Determine whether this token has the static keyword.
     *
     * Please note that the iterator cursor does not change due to this method
     *
     * @param DocBlox_Reflection_TokenIterator $tokens The iterator with tokens.
     *
     * @return DocBlox_Reflection_Token|null
     */
    protected function findStatic(DocBlox_Reflection_TokenIterator $tokens)
    {
        return $tokens->findPreviousByType(T_STATIC, 5, array('{', ';'));
    }

    /**
     * Searches for visibility specifiers with the current token.
     *
     * @param DocBlox_Reflection_TokenIterator $tokens Token iterator to
     *     search in.
     *
     * @return string public|private|protected
     */
    protected function findVisibility(DocBlox_Reflection_TokenIterator $tokens)
    {
        $result = 'public';
        $result = $tokens->findPreviousByType(T_PRIVATE, 5, array('{', ';'))
            ? 'private' : $result;
        $result = $tokens->findPreviousByType(T_PROTECTED, 5, array('{', ';'))
            ? 'protected' : $result;

        return $result;
    }

    /**
     * Sets the name for this Reflection Object.
     *
     * @param string $name String with unlimited length.
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function setName($name)
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException('Expected name to be a string');
        }

        $this->name = $name;
    }

    /**
     * Returns the name for this Reflection object.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the file name for this file.
     *
     * @param string $filename The path of this file.
     *
     * @return void
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Sets the name of the namespace to which this belongs.
     *
     * @param string $namespace Full name of namespace.
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function setNamespace($namespace)
    {
        if (!is_string($namespace)) {
            throw new InvalidArgumentException(
                'Expected the namespace to be a string'
            );
        }

        $this->namespace = $namespace;
    }

    /**
     * Returns the name of the namespace to which this belongs.
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Sets the list of namespace aliases in the parent file..
     *
     * @param string[] $namespace_aliases List of aliases to apply.
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function setNamespaceAliases($namespace_aliases)
    {
        if (!is_array($namespace_aliases)) {
            throw new InvalidArgumentException(
                'Expected the namespace aliases to be an array of strings'
            );
        }

        $this->namespace_aliases = $namespace_aliases;
    }

    /**
     * Returns the namespace aliases which can be applied to the types in
     * this object.
     *
     * @return string
     */
    public function getNamespaceAliases()
    {
        return $this->namespace_aliases;
    }

    /**
     * Returns the line number where this token starts.
     *
     * @return int
     */
    public function getLineNumber()
    {
        return $this->line_start;
    }

    /**
     * Getter; returns the token id which identifies the start of this object.
     *
     * @return int
     */
    public function getStartTokenId()
    {
        return $this->token_start;
    }

    /**
     * Returns the token id which identifies the end of the object.
     *
     * @return int
     */
    public function getEndTokenId()
    {
        return $this->token_end;
    }

    /**
     * Helper used to merge a given XML string into a given DOMDocument.
     *
     * @param DOMDocument $origin Destination to merge the XML into.
     * @param string      $xml    The XML to merge with the document.
     *
     * @return void
     */
    protected function mergeXmlToDomDocument(DOMDocument $origin, $xml)
    {
        $dom_arguments = new DOMDocument();
        $dom_arguments->loadXML(trim($xml));

        $this->mergeDomDocuments($origin, $dom_arguments);
    }

    /**
     * Helper method which merges a $document into $origin.
     *
     * @param DOMDocument $origin   The document to accept the changes.
     * @param DOMDocument $document The changes which are to be merged into
     *     the origin.
     *
     * @return void
     */
    protected function mergeDomDocuments(
        DOMDocument $origin,
        DOMDocument $document
    ) {
        $xpath = new DOMXPath($document);
        $qry = $xpath->query('/*');
        for ($i = 0; $i < $qry->length; $i++) {
            $origin->documentElement->appendChild(
                $origin->importNode($qry->item($i), true)
            );
        }
    }

    /**
     * Returns the name of the file to which this element is related.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Returns an XML representation of this object.
     *
     * @abstract
     *
     * @return string
     */
    abstract public function __toXml();

    /**
     * Default behavior of the toString method is to return the name of this
     * reflection.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

}