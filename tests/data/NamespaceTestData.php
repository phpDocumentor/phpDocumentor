<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category   phpDocumentor
 * @package    Parser
 * @subpackage Tests
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */

namespace foo;

use My\Full\Classname as Another;

// this is the same as use My\Full\NSname as NSname
use My\Full\NSname;

// importing a global class
use \ArrayObject;

/**
 * Namespace test.
 *
 * @category   phpDocumentor
 * @package    Parser
 * @subpackage Tests
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
class NamespaceTest
{
    /**
    * Expected type is foo\Classname
    *
    * @var Classname
    */
    public $singleNameClass = null;

    /**
    * Expected type is My\Full\Classname
    *
    * @var My\Full\Classname
    */
    public $namespacedClass = null;

    /**
    * Expected type is \ArrayObject
    *
    * @var \ArrayObject
    */
    public $globalClass = null;

    /**
    * Expected type is foo\Another
    *
    * @var namespace\Another
    */
    public $sameSpaceClassAnother = null;

    /**
    * Expected type is My\Full\Classname
    *
    * @var Another
    */
    public $aliasClassAnother = null;

    /**
    * Expected type is My\Full\NSname\subns
    *
    * @var NSname\subns
    */
    public $aliasSpaceNSname = null;

    /**
    * Expected type is \ArrayObject
    *
    * @var ArrayObject
    */
    public $aliasGlobalClass = null;
}

/**
 * Contains the test data for the phpDocumentor parser.
 *
 * This is a long description
 *
 * @category   phpDocumentor
 * @package    Parser
 * @subpackage Tests
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
class DocBlocTest
{
    /** @var string $a */
    public $a = '';


    /**
    * This is a multi-line test where
    * we want to see if it works.
    *
    * We include a long description as well
    * that spans multiple lines.
    * {@link http://www.github.com/phpdocumentor/phpdocumentor2}
    *
    * @return void
    */
    public function function1()
    {

    }

    /**
    * Only a single line.
    *
    * @return void
    */
    public function function2()
    {

    }

    /**
    * Multiline short description
    * but intentionally did not end with a dot
    *
    * long description
    *
    * @return void
    */
    public function function3()
    {

    }

    /**
    * Only a short description
    */
    public function function4()
    {

    }
    /**
    * Multiline short description
    * but intentionally did not @end with a dot and forgot extra newline
    * long @description
    *
    * @param string[] $test
    *
    * @return void
    */
    public function function5($test)
    {

    }
}

/**
 * Separate test function.
 *
 * @param string[]|int[] $param
 *
 * @return void
 */
function test(array $param)
{
}
