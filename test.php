<?php
/**
 * File docblock
 *
 * @package Nabu
 */

/**
 * Function docblock for SingleFunction
 *
 * @param int $argument
 *
 * @return void
 */
function single_function($argument = 'test')
{

}

/**
 * This is a test method
 *
 * @return int description
 */
function test_function()
{

}

/**
 * This is a test method
 *
 * @return int description
 */
function test_function2()
{

}



/**
 * test.
 *
 * This is a test
 *
 * @author Mike
 */
class SingleClass
{
  private $test = null;
  private $test2 = array(
    'test' => 1
  );
  private $test3 = 1;
  private $test4 = true;

  /**
   * Method Docblock for StaticPublicMethod
   *
   * @param array $argument_a test argument
   * @param FooBarClass $argument_b test argument
   *
   * @return void
   */
  static public function StaticPublicMethod(array $argument_a, FooBarClass $argument_b = null)
  {
  }

  /**
   * Method Docblock
   *
   * @param int $argument test argument
   *
   * @return void
   */
  public function PublicMethod($argument)
  {
  }

  protected function ProtectedMethod()
  {
  }
}

abstract class FooBarClass extends SingleClass implements Reflector, Traversable
{
  const TEST = 'test2';
}