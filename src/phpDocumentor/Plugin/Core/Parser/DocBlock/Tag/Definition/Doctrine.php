<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category   phpDocumentor
 * @package    Parser
 * @subpackage Tag_Definitions
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core\Parser\DocBlock\Tag\Definition;

/**
 * Definition for all Doctrine tags to explode them into subcomponents so they
 * can be better processed in the transformation.
 *
 * The Doctrine tags follow a deviating syntax from default PHPDoc which is
 * described using the following EBNF:
 *
 *     Annotation      ::= "@" AnnotationName ["(" [Values] ")"]
 *     AnnotationName  ::= QualifiedName | SimpleName
 *     QualifiedName   ::= NameSpacePart "\" {NameSpacePart "\"}* SimpleName
 *     NameSpacePart   ::= identifier
 *     SimpleName      ::= identifier | null | false | true
 *     Values          ::= Array | Value {"," Value}*
 *
 *     Value           ::= PlainValue | FieldAssignment
 *     PlainValue      ::= integer|string|float|boolean|Array|Annotation
 *     FieldAssignment ::= FieldName "=" PlainValue
 *     FieldName       ::= identifier
 *     Array           ::= "{" ArrayEntry {"," ArrayEntry}* "}"
 *     ArrayEntry      ::= Value | KeyValuePair
 *     KeyValuePair    ::= Key "=" PlainValue
 *     Key             ::= string | integer
 *
 * This means that a Doctrine Tag may have the following forms:
 *
 *     * @Column(type="string", length=32, unique=true, nullable=false)
 *
 * or
 *
 *     * @DiscriminatorMap({"person" = "Person", "employee" = "Employee"})
 *
 * but also the FQCN notation:
 *
 *     * @MyCompany\Annotations\Foo
 *
 * An important note is that the opening ( of the parameter list and the
 * Annotation name do not have any whitespace.
 *
 * @category   phpDocumentor
 * @package    Parser
 * @subpackage Tag_Definitions
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
class Doctrine extends Definition
{

    /**
     * Adds a new attribute `refers` to the structure element for this tag and
     * set the description to the element name.
     *
     * @return void
     */
    protected function configure()
    {
        $description = trim($this->xml->getAttribute('description'));
        // remove enclosing parenthesis
        if ($description) {
            $this->xml->setAttribute(
                'description', substr($description, 1, -1)
            );
        }

        // add indicator that we are talking about Doctrine Tags
        $this->xml->setAttribute('plugin', 'doctrine');

        $name = $this->xml->getAttribute('name');
        if (strpos($name, '\\') !== false) {
            $name = substr($name, strrpos($name, '\\')+1);
        }

        // add a link to the documentation for this annotation
        $this->xml->setAttribute(
            'link',
            'http://www.doctrine-project.org/docs/orm/2.1/en/'
            . 'reference/annotations-reference.html#annref-'
            . strtolower($name)
        );

        $this->xml->setAttribute('content', $this->tag->getDescription());
        if ('' === $description) {
            $this->xml->setAttribute('description', $name);
        }

        // find the array of arguments
        $arguments = $this->findArguments($this->xml->getAttribute('content'));
        foreach ($arguments as $argument) {
            $arg = $this->xml->appendChild(
                new \DOMElement('argument', $argument[1])
            );
            $arg->setAttribute('field-name', $argument[0]);
        }
    }

    /**
     * Search for the Arguments and return them as arrays with a FieldName
     * and Value.
     *
     * See the EBFN in the class' documentation for the business rules used to
     * split the Annotation's arguments.
     *
     * @param string $description The arguments string to parse.
     *
     * @return array[]
     */
    protected function findArguments($description)
    {
        $arguments           = array();
        $level               = 0;
        $doublequoted_string = false;
        $quoted_string       = false;
        $key                 = '';
        $value               = '';

        for ($i = 0; $i < strlen($description); $i++) {
            switch ($description[$i]) {
            case '{':
                $level++;
                continue 2;
            case '}':
                $level--;
                continue 2;
            case '"':
                if (!isset($description[$i - 1])
                    || ($description[$i - 1] != '\\')
                ) {
                    $doublequoted_string = !$doublequoted_string;
                }
                break;
            case '\'':
                if (!isset($description[$i - 1])
                    || ($description[$i - 1] != '\\')
                ) {
                    $quoted_string = !$quoted_string;
                }
                break;
            case '=':
                if (($level == 0)
                    && !$doublequoted_string
                    && !$quoted_string
                ) {
                    $key = $value;
                    $value = '';
                    continue 2;
                }
                break;
            case ',':
                if (($level == 0)
                    && !$doublequoted_string
                    && !$quoted_string
                ) {
                    $arguments[] = array(trim($key), trim($value));
                    $key = $value = '';
                    continue 2;
                }
                break;
            }

            $value .= $description[$i];
        }
        if ($key != '' || $value != '') {
            $arguments[] = array(trim($key), trim($value));
            $key = $value = '';
        }

        return $arguments;
    }
}
