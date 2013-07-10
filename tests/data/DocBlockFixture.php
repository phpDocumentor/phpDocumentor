<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category   phpDocumentor
 * @package    Transformer
 * @subpackage Unit_tests
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */

/**
 * Fixture file for different DocBlock tests.
 *
 * @category   phpDocumentor
 * @package    Transformer
 * @subpackage Unit_tests
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
class phpDocumentor_Tests_Data_DocBlockFixture
{
    /*
    * The class docblock has two lines of short description AND the short description ends with a space.
    */

    /**
    *
    */
    public static function EmptyDocBlock(ArrayObject $object)
    {
        // test a method with an empty docblock
    }

    /** */
    public static function ReallyEmptyDocBlock(ArrayObject $object)
    {
        // test a method with the smallest thinkable docblock
    }

    /**
    * Single line docblock.
    */
    public static function SingleLineDocBlock(ArrayObject $object)
    {
        // test a method with an empty docblock
    }

    /** Single line docblock. */
    public static function SingleLineDocBlock2(ArrayObject $object)
    {
        // test a method with an empty docblock
    }

    /**
    * Single line docblock.
    * Long description.
    */
    public static function SimpleDocBlockWithLD(ArrayObject $object)
    {
        // test a method with an empty docblock
    }

    /**
    * This docblock is the ideal situation, short descriptions are single line and closed with a point.
    *
    * The long description is separated a whiteline away and has a trailing whiteline. After which each
    * tag 'group' is separated by a whiteline.
    *
    * @static
    *
    * @param ArrayObject $object Ideally.
    *
    * @return string
    */
    public static function IdealDocBlock(ArrayObject $object)
    {
    }

    /**
    * This docblock is invalid because the short description 'does not end'
    * @static
    * @param ArrayObject $object
    * @return string
    */
    public static function DocBlockWithInvalidShortDescription($object)
    {
        /*
         * This Docblock's short description does not end with a . or with a double space and thus
         * should be invalid. We allow it by noticing that there are tags following.
         */
    }

    /**
    * This DocBlock tests whether the @link tag is correctly taken and shown.
    *
    * @link http://www.phpdoc.org
    *
    * @return void
    */
    public static function DocBlockWithLinkTag()
    {

    }

    /**
    * This tests whether a custom tag with hypen is interpreted
    *
    * @custom-tag This is a custom tag
    *
    * @return void
    */
    public static function DocBlockWithTagWithHyphen()
    {

    }

    /**
    * This docblock will contain an inline tag to test whether it still crashes.
    *
    * This is a test {@link} with an inline tag.
    *
    * @static
    *
    * @return void
    */
    public static function DocBlockWithInlineTag()
    {

    }
}
