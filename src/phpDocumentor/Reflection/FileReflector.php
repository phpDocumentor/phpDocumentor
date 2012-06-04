<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection;

/**
 * Reflection class for a full file.
 *
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://phpdoc.org
 */
class FileReflector extends \PHPParser_NodeVisitorAbstract
{
    protected $hash = array('a' => 1, 'b' => 2);
    protected $contents = 1;
    protected $includes = array();
    protected $constants = array();
    protected $classes = array();
    protected $traits = array();
    protected $interfaces = array();
    protected $functions = array();
    protected $filename = '';
    protected $doc_block;
    protected $default_package_name = 'Default';

    /** @var string[] A list of markers contained in this file. */
    protected $markers = array();

    /** @var string[] A list of errors during processing */
    protected $parse_markers = array();

    /** @var string[] A list of all marker types to search for in this file. */
    protected $marker_terms = array('TODO', 'FIXME');

    protected $namespace_aliases = array();
    protected $current_namespace = '';

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
     * @throws \phpDocumentor\Reflection\Exception when the filename is incorrect or
     *   the file can not be opened
     */
    public function __construct($file, $validate = false)
    {
        if (!is_string($file) || (!is_readable($file))) {
            throw new \phpDocumentor\Reflection\Exception(
                'The given file should be a string, should exist on the '
                . 'filesystem and should be readable'
            );
        }

        if ($validate) {
            exec('php -l ' . escapeshellarg($file), $output, $result);
            if ($result != 0) {
                throw new \phpDocumentor\Reflection\Exception(
                    'The given file could not be interpreted as it contains '
                    . 'errors: ' . implode(PHP_EOL, $output)
                );
            }
        }

        $this->filename = $file;
        $this->contents = file_get_contents($file);

        // filemtime($file) is sometimes between 0.00001 and 0.00005 seconds
        // faster but md5 is more accurate
        // real world tests with larger code bases should determine how much
        // it really matters
        $this->hash = md5($this->contents);
    }

    public function process()
    {
        $traverser = new Traverser();
        $traverser->visitor = $this;
        $traverser->traverse($this->contents);

        $this->scanForMarkers();
    }

    public function getClasses()
    {
        return $this->classes;
    }

    public function getTraits()
    {
        return $this->traits;
    }

    public function getConstants()
    {
        return $this->constants;
    }

    public function getFunctions()
    {
        return $this->functions;
    }

    public function getIncludes()
    {
        return $this->includes;
    }

    public function getInterfaces()
    {
        return $this->interfaces;
    }

    public function enterNode(\PHPParser_Node $node)
    {
        switch(get_class($node)) {
            case 'PHPParser_Node_Stmt_Use':
                /** @var \PHPParser_Node_Stmt_UseUse $use */
                foreach ($node->uses as $use) {
                    $this->namespace_aliases[$use->alias]
                        = implode('\\', $use->name->parts);
                }
                break;
            case 'PHPParser_Node_Stmt_Namespace':
                $this->current_namespace = implode('\\', $node->name->parts);
                break;
            case 'PHPParser_Node_Stmt_Class':
                $this->classes[] = new ClassReflector($node);
                break;
            case 'PHPParser_Node_Stmt_Trait':
                $this->traits[] = new TraitReflector($node);
                break;
            case 'PHPParser_Node_Stmt_Interface':
                $this->interfaces[] = new InterfaceReflector($node);
                break;
            case 'PHPParser_Node_Stmt_Function':
                $this->functions[] = new FunctionReflector($node);
                break;
            case 'PHPParser_Node_Stmt_Const':
                foreach ($node->consts as $constant) {
                    $reflector = new ConstantReflector($constant);
                    $this->constants[$reflector->getName()] = $reflector;
                }
                break;
            case 'PHPParser_Node_Expr_FuncCall':
                if ($node->name instanceof \PHPParser_Node_Name
                    && $node->name == 'define'
                ) {
                    $constant = new \PHPParser_Node_Const(
                        $node->args[0]->value->value, $node->args[1]->value
                    );
                    $constant->setLine($node->getLine());
                    $constant->namespacedName = new \PHPParser_Node_Name(
                        $this->current_namespace.'\\'.$constant->name
                    );

                    $reflector = new ConstantReflector($constant);
                    $this->constants[$reflector->getName()] = $reflector;
                }
                break;
            case 'PHPParser_Node_Expr_Include':
                $this->includes[] = new IncludeReflector($node);
                break;
        }
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function getDocBlock()
    {
        return $this->doc_block;
    }

    public function getDefaultPackageName()
    {
        return $this->default_package_name;
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
     * @see phpDocumentor\Reflection\FileReflector::addMarker()
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

    public function getMarkers()
    {
        return $this->markers;
    }

    /**
     * Adds a parse error to the system
     *
     * @param string[] $data An array (or object approachable as array such as
     * \sfEvent) that contains the type, message, line and code element.
     *
     * @return void
     */
    public function addParserMarker($data)
    {
        $this->parse_markers[] = array(
            $data['type'],
            $data['message'],
            $data['line'],
            $data['code']
        );
    }

    /**
     * Scans the file for markers and records them in the markers property.
     *
     * @see getMarkers()
     *
     * @todo this method may incur a performance penalty while the AST also
     * contains the comments. This method should be replaced by a piece of
     * code that interprets the comments in the AST.
     * This has not been done since that may be an extensive refactoring (each
     * PHPParser_Node* contains a 'comments' attribute and must thus recursively
     * be discovered)
     *
     * @return void
     */
    public function scanForMarkers()
    {
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

    public function getParseErrors()
    {
        return $this->parse_markers;
    }

    public function getNamespaceAliases()
    {
        return $this->namespace_aliases;
    }

    public function getContents()
    {
        return $this->contents;
    }

    public function setDefaultPackageName($default_package_name)
    {
        $this->default_package_name = $default_package_name;
    }

    public function setFilename($filename)
    {
        $this->filename = $filename;
    }
}
