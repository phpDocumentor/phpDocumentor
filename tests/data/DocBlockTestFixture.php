<?php
/**
 * AgaviContext provides information about the current application context,
 * such as the module and action names and the module directory.
 * It also serves as a gateway to the core pieces of the framework, allowing
 * objects with access to the context, to access other useful objects such as
 * the current controller, request, user, database manager etc.
 *
 * @package    agavi
 * @subpackage core
 *
 * @author     Sean Kerr <skerr@mojavi.org>
 * @author     Mike Vincent <mike@agavi.org>
 * @author     David Zülke <dz@bitxtender.com>
 * @copyright  Authors
 * @copyright  The Agavi Project
 *
 * @since      0.9.0
 *
 * @version    $Id: AgaviContext.class.php 4399 2010-01-11 16:41:20Z david $
 */
class DocBlockTestFixture
{
  /*
   * The class docblock has two lines of short description AND the short description ends with a space.
   */

  /**
   *
   */
  static public function EmptyDocBlock(ArrayObject $object)
  {
    // test a method with an empty docblock
  }

  /** */
  static public function ReallyEmptyDocBlock(ArrayObject $object)
  {
    // test a method with the smallest thinkable docblock
  }

  /**
   * Single line docblock.
   */
  static public function SingleLineDocBlock(ArrayObject $object)
  {
    // test a method with an empty docblock
  }

  /** Single line docblock. */
  static public function SingleLineDocBlock2(ArrayObject $object)
  {
    // test a method with an empty docblock
  }

  /**
   * Single line docblock.
   * Long description.
   */
  static public function SimpleDocBlockWithLD(ArrayObject $object)
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
  static public function IdealDocBlock(ArrayObject $object)
  {
  }

  /**
   * This docblock is invalid because the short description 'does not end'
   * @static
   * @param ArrayObject $object
   * @return string
   */
  static public function DocBlockWithInvalidShortDescription($object)
  {
    /*
     * This Docblock's short description does not end with a . or with a double space and thus
     * should be invalid. We allow it by noticing that there are tags following.
     */
  }

  /**
   * This DocBlock tests whether the @link tag is correctly taken and shown.
   *
   * @link http://www.docblox-project.org
   *
   * @return void
   */
  static public function DocBlockWithLinkTag()
  {

  }

  /**
   * This tests whether a custom tag with hypen is interpreted
   *
   * @custom-tag This is a custom tag
   *
   * @return void
   */
  static public function DocBlockWithTagWithHyphen()
  {

  }

}