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

if (!defined('T_NAMESPACE')) {
    /** @var int This constant is PHP 5.3+, but is necessary for correct parsing */
    define('T_NAMESPACE', 377);
}

/**
 * Reflection class for a full file.
 *
 * @category DocBlox
 * @package  Static_Reflection
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://docblox-project.org
 */
class DocBlox_Reflection_File extends DocBlox_Reflection_DocBlockedAbstract
{
    /** @var DocBlox_Token_Iterator */
    protected $tokens = null;

    /** @var string Contents of the given file. */
    protected $contents = '';

    /** @var string|null A unique (MD5) hash of this file */
    protected $hash = null;

    /** @var DocBlox_Reflection_Interface[] Every contained interface */
    protected $interfaces = array();

    /** @var DocBlox_Reflection_Class[] every contained class */
    protected $classes = array();

    /** @var DocBlox_Reflection_Function[] Every contained function */
    protected $functions = array();

    /** @var DocBlox_Reflection_Constant[] Every contained global constants */
    protected $constants = array();

    /** @var DocBlox_Reflection_Include[] Every contained include and require */
    protected $includes = array();

    /** @var string The currently active namespace is during parsing. */
    protected $active_namespace = 'default';

    /** @var string[] A list of markers contained in this file. */
    protected $markers = array();

    /** @var string[] A list of all marker types to search for in this file. */
    protected $marker_terms = array('TODO', 'FIXME');

    /**
     * Opens the file and retrieves its contents.
     *
     * During construction the given file is checked whether it is readable and
     * if the $validate argument is true a PHP Lint action is executed to
     * check whether the there are no parse errors.
     *
     * By default the Lint check is disable because of the performance hit
     * introduced by this action.
     *
     * If the validation checks out the file's contents are read; converted to
     * UTF-8 and the has is created from those contents.
     *
     * @param string  $file     Name of the file.
     * @param boolean $validate Whether to check the file using PHP Lint.
     *
     * @throws DocBlox_Reflection_Exception when the filename is incorrect or
     *   the file can not be opened
     *
     * @return void
     */
    public function __construct($file, $validate = false)
    {
        parent::__construct();

        if (!is_string($file) || (!is_readable($file))) {
            throw new DocBlox_Reflection_Exception(
                'The given file should be a string, should exist on the '
                . 'filesystem and should be readable'
            );
        }

        if ($validate) {
            exec('php -l ' . escapeshellarg($file), $output, $result);
            if ($result != 0) {
                throw new DocBlox_Reflection_Exception(
                    'The given file could not be interpreted as it contains '
                    . 'errors: ' . implode(PHP_EOL, $output)
                );
            }
        }

        $this->setFilename($file);
        $this->name = $this->filename;
        $this->contents = $this->convertToUtf8($file, file_get_contents($file));

        // filemtime($file) is sometimes between 0.00001 and 0.00005 seconds
        // faster but md5 is more accurate
        // real world tests with larger code bases should determine how much
        // it really matters
        $this->setHash(md5($this->contents));
    }

    /**
     * Converts a piece of text to UTF-8 if it isn't.
     *
     * This method tries to detect the encoding of the given string when
     * necessary using the finfo extension (packaged with PHP 5.3 by default;
     * available as extension before that) and then uses iconv to convert the
     * contents from the found encoding to UTF-8.
     *
     * If the finfo class is not available we try to detect the encoding using
     * the mbstring extension and if that isn't available then we try to
     * brute-force detect the encoding using iconv.
     *
     * If the encoding does not register as UTF-8 we try to re-encode to UTF-8;
     * if that fails the original content is returned and en error is logged.
     *
     * @param string $filename Path to the file; finfo directly reads the file
     *      instead of the contents.
     * @param string $contents File's contents to convert.
     *
     * @return string
     */
    protected function convertToUtf8($filename, $contents)
    {
        $encoding = null;

        // empty files need not be converted (even worse: finfo detects them
        // as binary!)
        if (trim($contents) === '') {
            return '';
        }

        // detect encoding and transform to UTF-8
        if (class_exists('finfo')) {
            // PHP 5.3 or PECL extension
            $flag     = defined('FILEINFO_MIME_ENCODING')
                ? FILEINFO_MIME_ENCODING
                : FILEINFO_MIME;
            $info     = new finfo();
            $encoding = $info->file($filename, $flag);

            // Versions prior to PHP 5.3 do not support the
            // FILEINFO_MIME_ENCODING constant; extract the encoding from the
            // FILEINFO_MIME result; wo do nto do this by default for
            // performance reasons
            if ($flag != FILEINFO_MIME_ENCODING) {
                $encoding = explode('=', $encoding);
                if (count($encoding) != 2) {
                    throw new InvalidArgumentException(
                        'Mime type returned by finfo contains multiple parts '
                        . 'separated by an equals sign, only one is expected'
                    );
                }

                $encoding = $encoding[1];
            }
        } elseif (function_exists('mb_detect_encoding')) {
            // OR with mbstring
            $encoding = mb_detect_encoding($contents);
        } elseif (function_exists('iconv')) {
            // OR using iconv (performance hit)
            $this->log(
                'Neither the finfo nor the mbstring extensions are active; '
                . 'special character handling may not give the best results',
                Zend_Log::WARN
            );
            $encoding = $this->_detectEncodingFallback($contents);
        } else {
            // or not..
            $this->log(
                'Unable to handle character encoding; finfo, mbstring and '
                . 'iconv extensions are not enabled',
                Zend_Log::CRIT
            );
        }

        // if the encoding is unknown-8bit or x-user-defined we assume it might
        // be latin1; otherwise iconv will fail
        if (($encoding == 'unknown-8bit') || ($encoding == 'x-user-defined')) {
            $encoding = 'latin1';
        }

        // convert if a source encoding is found; otherwise we throw an error
        // and have to continue using the given data
        if (($encoding !== null) && (strtolower($encoding) != 'utf-8')) {
            $tmp_contents = iconv($encoding, 'UTF-8//IGNORE', $contents);
            if ($tmp_contents === false) {
                $this->log(
                    'Encoding of file ' . $filename . ' from ' . $encoding
                    . ' to UTF-8 failed, please check the notice for a '
                    . 'detailed error message',
                    Zend_Log::EMERG
                );
            } else {
                $contents = $tmp_contents;
            }
        }

        return $contents;
    }

    /**
     * This is a fallback mechanism to detect the encoding of  a string; if no
     * finfo or mbstring extension is present then this is used.
     *
     * WARNING: try to prevent this; it is assumed that this method is not
     * fool-proof nor performing as well as the other options.
     *
     * @param string $string String to detect the encoding of.
     *
     * @return string Name of the encoding to return.
     */
    private function _detectEncodingFallback($string)
    {
        static $list = array(
            'UTF-8',
            'ASCII',
            'ISO-8859-1',
            'UTF-7',
            'WINDOWS-1251'
        );

        foreach ($list as $item) {
            $sample = iconv($item, $item, $string);
            if (md5($sample) == md5($string)) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Adds a marker to scan the contents of this file for.
     *
     * @param string $name The Marker term, i.e. FIXME or TODO.
     *
     * @return void
     */
    public function addMarker($name)
    {
        $this->marker_terms[] = $name;
    }

    /**
     * Sets a list of markers to search for.
     *
     * @param string[] $markers A list of marker terms to scan for.
     *
     * @see DocBlox_Reflection_File::addMarker()
     *
     * @return void
     */
    public function setMarkers(array $markers)
    {
        $this->marker_terms = array();

        foreach ($markers as $marker) {
            $this->addMarker($marker);
        }
    }

    /**
     * Returns the hash identifying this file.
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Sets the hash which identifies this file.
     *
     * @param string $hash A piece of text (possibly MD5 hash) which
     *      identifies the contents of this file.
     *
     * @return void
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * Returns all classes in this file.
     *
     * @return DocBlox_Reflection_Class
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * Returns the class object with the given name.
     *
     * @param string $name Name of the requested class.
     *
     * @return DocBlox_Reflection_Class|null
     */
    public function getClass($name)
    {
        return isset($this->classes[$name]) ? $this->classes[$name] : null;
    }

    /**
     * Extracts all tokens from this file and initializes the iterator used to
     * walk through this file.
     *
     * @param string $contents The text to parse into tokens.
     * @param string $filename The path of this file, if present.
     *
     * @return DocBlox_Token_Iterator
     */
    public function initializeTokens($contents, $filename = null)
    {
        $tokens = token_get_all($contents);
        $this->debug(
            count($tokens) . ' tokens found in class ' . $this->getName()
        );

        $tokens = new DocBlox_Token_Iterator($tokens);
        if ($filename != null) {
            $tokens->setFilename($filename);
        }

        return $tokens;
    }

    /**
     * Starts the parsing of this file.
     *
     * @return void
     */
    public function process()
    {
        $tokens = $this->initializeTokens($this->contents, $this->filename);
        $this->parseTokenizer($tokens);
    }

    /**
     * Hook method where you can determine the generic properties for this file
     * before it is scanned for structures.
     *
     * @param DocBlox_Token_Iterator $tokens Token array to parse.
     *
     * @return void
     */
    protected function processGenericInformation(DocBlox_Token_Iterator $tokens)
    {
        // find file docblock; standard function does not suffice as this scans
        // backwards and we have to make sure it isn't the docblock of
        // another element
        $this->doc_block = $this->findDocBlock($tokens);

        // find all markers, get the entire file and check for marker terms.
        $marker_data = array();
        foreach (explode("\n", $this->contents) as $line_number => $line) {
            preg_match_all(
                '~//[\s]*(' . implode('|', $this->marker_terms) . ')\:?[\s]*(.*)~',
                $line,
                $matches, PREG_SET_ORDER
            );
            foreach ($matches as &$match) {
                $match[3] = $line_number + 1;
            }
            $marker_data = array_merge($marker_data, $matches);
        }

        // store marker results and remove first entry (entire match),
        // this results in an array with 2 entries:
        // marker name and content
        $this->markers = $marker_data;
        foreach ($this->markers as &$marker) {
            array_shift($marker);
        }
    }

    /**
     * Tries to find the DocBlox belonging to this file (page-level DocBlock).
     *
     * A page level DocBlock is a DocBlock that is at the top of a file and
     * is not directly followed by a class definition.
     * Page level DocBlocks MUST contain a @package tag or they are
     * 'disqualified'.
     *
     * If no page level docblox is found we throw a warning to indicate to the
     * user that this is missing.
     *
     * @param DocBlox_Token_Iterator $tokens Token array to parse.
     *
     * @return DocBlox_Reflection_DocBlock|null
     */
    public function findDocBlock(DocBlox_Token_Iterator $tokens)
    {
        $result   = null;
        $docblock = $tokens->findNextByType(
            T_DOC_COMMENT, 10, array(T_CLASS, T_NAMESPACE)
        );

        try {
            $result = $docblock
                ? new DocBlox_Reflection_DocBlock($docblock->getContent())
                : null;
        }
        catch (Exception $e) {
            $this->log($e->getMessage(), Zend_Log::CRIT);
        }

        // TODO: add a check if a class immediately follows this docblock, if so
        // this is not a page level docblock but a class docblock
        $valid = $this->validateDocBlock(
            $this->filename,
            $docblock ? $docblock->getLineNumber() : 0,
            $result
        );

        if (!$valid) {
            $result = null;
        }

        return $result;
    }

    /**
     * Iterates through all tokens and when one is found we invoke the
     * processToken method to handle the details.
     *
     * @param DocBlox_Token_Iterator $tokens The list of tokens to scan.
     *
     * @see DocBlox_Reflection_Abstract::processTokens
     * @see DocBlox_Reflection_Abstract::processToken
     *
     * @return void
     */
    public function processTokens(DocBlox_Token_Iterator $tokens)
    {
        $token = null;
        while ($tokens->valid()) {
            /** @var DocBlox_Token $token */
            $token = $token === null ? $tokens->current() : $tokens->next();

            if (($token instanceof DocBlox_Token) && $token->getType()) {
                $this->processToken($token, $tokens);
            }
        }
    }

    /**
     * Processes the T_USE token and extracts all namespace aliases.
     *
     * @param DocBlox_Token_Iterator $tokens Tokens to interpret with the
     *      pointer at the token to be processed.
     *
     * @return void
     */
    protected function processUse(DocBlox_Token_Iterator $tokens)
    {
        /** @var DocBlox_Token $token */
        $aliases = array('');
        while (($token = $tokens->next()) && ($token->getContent() != ';')) {
            // if a comma is found, go to the next alias
            if (!$token->getType() && $token->getContent() == ',') {
                $aliases[] = '';
                continue;
            }

            $aliases[count($aliases) - 1] .= $token->getContent();
        }

        $result = array();
        foreach ($aliases as $key => $alias) {
            // an AS is always surrounded by spaces; by trimming the $alias we
            // then know that the first element is the namespace and the last
            // is the alias.
            // We explicitly do not use spliti to prevent regular expressions
            // for performance reasons (the AS may be any case).
            $alias = explode(' ', trim($alias));

            // if there is only one part, that means no AS is given and the
            // last segment of the namespace functions as alias.
            if (count($alias) == 1) {
                $alias_parts = explode('\\', $alias[0]);
                $alias[] = $alias_parts[count($alias_parts) - 1];
            }

            $result[$alias[count($alias) - 1]] = $alias[0];
            unset($aliases[$key]);
        }

        $this->namespace_aliases = array_merge(
            $this->namespace_aliases,
            $result
        );
    }

    /**
     * Changes the active namespace indicator when a namespace token is
     * encountered to indicate that the space has changed.
     *
     * @param DocBlox_Token_Iterator $tokens Tokens to interpret with the
     *      pointer at the token to be processed.
     *
     * @return void
     */
    protected function processNamespace(DocBlox_Token_Iterator $tokens)
    {
        // collect all namespace parts
        $namespace = array();
        while ($token = $tokens->gotoNextByType(T_STRING, 5, array(';', '{'))) {
            $namespace[] = $token->getContent();
        }
        $namespace = implode('\\', $namespace);

        $this->active_namespace = $namespace;
    }

    /**
     * Parses an interface definition and adds it to the interfaces array.
     *
     * @param DocBlox_Token_Iterator $tokens Tokens to interpret with the
     *      pointer at the token to be processed.
     *
     * @return void
     */
    protected function processInterface(DocBlox_Token_Iterator $tokens)
    {
        $this->resetTimer('interface');

        $interface = new DocBlox_Reflection_Interface();
        $interface->setNamespace($this->active_namespace);
        $interface->setNamespaceAliases($this->namespace_aliases);
        $interface->parseTokenizer($tokens);

        $this->debugTimer(
            '>> Processed interface ' . $interface->getName(),
            'interface'
        );

        $this->interfaces[$interface->getName()] = $interface;
    }

    /**
     * Parses a class definition and adds it to the classes array.
     *
     * @param DocBlox_Token_Iterator $tokens Tokens to interpret with the
     *      pointer at the token to be processed.
     *
     * @return void
     */
    protected function processClass(DocBlox_Token_Iterator $tokens)
    {
        $this->resetTimer('class');

        $class = new DocBlox_Reflection_Class();
        $class->setFilename($this->filename);
        $class->setNamespace($this->active_namespace);
        $class->setNamespaceAliases($this->namespace_aliases);
        $class->parseTokenizer($tokens);

        $this->debugTimer('>> Processed class ' . $class->getName(), 'class');

        $this->classes[$class->getName()] = $class;
    }

    /**
     * Parses a function definition and adds it to the functions array.
     *
     * @param DocBlox_Token_Iterator $tokens Tokens to interpret with the
     *      pointer at the token to be processed.
     *
     * @return void
     */
    protected function processFunction(DocBlox_Token_Iterator $tokens)
    {
        $this->resetTimer('function');

        $function = new DocBlox_Reflection_Function();
        $function->setFilename($this->filename);
        $function->setNamespace($this->active_namespace);
        $function->setNamespaceAliases($this->namespace_aliases);
        $function->parseTokenizer($tokens);

        $this->debugTimer(
            '>> Processed function ' . $function->getName(),
            'function'
        );

        $this->functions[$function->getName()] = $function;
    }

    /**
     * Parses a global constant definition and adds it to the constants array.
     *
     * @param DocBlox_Token_Iterator $tokens Tokens to interpret with the
     *      pointer at the token to be processed.
     *
     * @return void
     */
    protected function processConst(DocBlox_Token_Iterator $tokens)
    {
        $this->resetTimer('constant');

        $constant = new DocBlox_Reflection_Constant();
        $constant->setFilename($this->filename);
        $constant->setNamespace($this->active_namespace);
        $constant->setNamespaceAliases($this->namespace_aliases);
        $constant->parseTokenizer($tokens);

        $this->debugTimer(
            '>> Processed constant ' . $constant->getName(),
            'constant'
        );

        $this->constants[$constant->getName()] = $constant;
    }

    /**
     * Parses any T_STRING token to find generic keywords to process.
     *
     * This token is used to find any:
     *
     * * `define`, thus constants which are defined using the define keyword
     * * Globals
     *
     * @param DocBlox_Token_Iterator $tokens Tokens to interpret with the
     *      pointer at the token to be processed.
     *
     * @todo implement globals support since the exact algorythm needs to be
     *      defined, see GH #68
     *
     * @return void
     */
    protected function processString(DocBlox_Token_Iterator $tokens)
    {
        /** @var DocBlox_Token $token  */
        $token = $tokens->current();
        switch ($token->getContent()) {
        case 'define':
            $this->resetTimer('constant');

            $constant = new DocBlox_Reflection_Constant();
            $constant->setFilename($this->filename);
            $constant->setNamespace($this->active_namespace);
            $constant->setNamespaceAliases($this->namespace_aliases);
            $constant->parseTokenizer($tokens);

            $this->debugTimer(
                '>> Processed define: ' . $constant->getName(),
                'constant'
            );

            $this->constants[$constant->getName()] = $constant;
            break;
        }
    }

    /**
     * Parses a require definition and adds it to the includes array.
     *
     * @param DocBlox_Token_Iterator $tokens Tokens to interpret with the
     *      pointer at the token to be processed.
     *
     * @return void
     */
    protected function processRequire(DocBlox_Token_Iterator $tokens)
    {
        $this->processInclude($tokens);
    }

    /**
     * Parses a require once definition and adds it to the includes array.
     *
     * @param DocBlox_Token_Iterator $tokens Tokens to interpret with the
     *      pointer at the token to be processed.
     *
     * @return void
     */
    protected function processRequireOnce(DocBlox_Token_Iterator $tokens)
    {
        $this->processInclude($tokens);
    }

    /**
     * Parses an include once definition and adds it to the includes array.
     *
     * @param DocBlox_Token_Iterator $tokens Tokens to interpret with the
     *      pointer at the token to be processed.
     *
     * @return void
     */
    protected function processIncludeOnce(DocBlox_Token_Iterator $tokens)
    {
        $this->processInclude($tokens);
    }

    /**
     * Parses an include definition and adds it to the includes array.
     *
     * This method is also used for the include once, require and require
     * once tokens.
     *
     * @param DocBlox_Token_Iterator $tokens Tokens to interpret with the
     *      pointer at the token to be processed.
     *
     * @see DocBlox_Reflection_File::processIncludeOnce
     * @see DocBlox_Reflection_File::processRequireOnce
     * @see DocBlox_Reflection_File::processRequire
     *
     * @return void
     */
    protected function processInclude(DocBlox_Token_Iterator $tokens)
    {
        $this->resetTimer('include');

        $include = new DocBlox_Reflection_Include();
        $include->setFilename($this->filename);
        $include->setNamespace($this->active_namespace);
        $include->parseTokenizer($tokens);

        $this->debugTimer(
            '>> Processed constant ' . $include->getName(),
            'include'
        );

        $this->includes[] = $include;
    }

    /**
     * Converts this file definition into a DocBlox compatible XML text.
     *
     * @return string
     */
    public function __toXml()
    {
        $xml = new SimpleXMLElement(
            '<file path="' . ltrim($this->filename, './') . '" hash="'
            . $this->hash . '"></file>'
        );
        $this->addDocblockToSimpleXmlElement($xml);

        // add markers
        foreach ($this->markers as $marker) {
            if (!isset($xml->markers)) {
                $xml->addChild('markers');
            }

            $marker_obj = $xml->markers->addChild(
                strtolower($marker[0]),
                trim($marker[1])
            );
            $marker_obj->addAttribute('line', $marker[2]);
        }

        // add namespace aliases
        foreach ($this->namespace_aliases as $alias => $namespace) {
            $alias_obj = $xml->addChild('namespace-alias', $namespace);
            $alias_obj->addAttribute('name', $alias);
        }

        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->loadXML(trim($xml->asXML()));

        foreach ($this->includes as $include) {
            $this->mergeXmlToDomDocument($dom, trim($include->__toXml()));
        }
        foreach ($this->constants as $constant) {
            $this->mergeXmlToDomDocument($dom, trim($constant->__toXml()));
        }
        foreach ($this->functions as $function) {
            $this->mergeXmlToDomDocument($dom, trim($function->__toXml()));
        }
        foreach ($this->interfaces as $interface) {
            $this->mergeXmlToDomDocument($dom, trim($interface->__toXml()));
        }
        foreach ($this->classes as $class) {
            $this->mergeXmlToDomDocument($dom, trim($class->__toXml()));
        }

        return trim($dom->saveXml());
    }

}