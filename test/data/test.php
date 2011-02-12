<?php
namespace foo;
use My\Full\Classname as Another;

// this is the same as use My\Full\NSname as NSname
use My\Full\NSname;

// importing a global class
use \ArrayObject;

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