<?php
/**
 * File docblock
 *
 * @package    Tokens
 * @subpackage Tests
 */

/**
 * @package    Tokens
 * @subpackage Tests
 */
namespace
{
    define('TEST', -1);
    define('TEST2', 1);
}

namespace Test\Tests5
{

    interface iTest extends \Countable
    {
    }

    abstract class test implements iTest
    {
        public function count()
        {
        }

        abstract public function count2();
    }

    require 'test2.php';
    require_once 'test2.php';
    include('test2.php');
    include_once 'test2.php';

    const GLOBAL_CONST = '1';
    const GLOBAL_CONST3 = 2, GLOBAL_CONST4 = '1';
    const GLOBAL_CONST2 = '2';

    /**
     * Function docblock for SingleFunction
     *
     * This is a code block:
     *
     *     block of code
     *
     * @param int $argument
     * @param Test\SingleClass $argument2 This is a test argument
     *
     * @return void
     */
    function single_function($argument = 'test', $argument2 = 'test2')
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


}
namespace Test
{

    /**
     * test.
     *
     * This is a class test
     *
     * @author Mike
     */
    class SingleClass
    {

        private $test = null;
        private $test2 = array('test' => 1);
        private $test3 = 1;

        /**
         * This is a property test
         *
         * @var boolean
         */
        private $test4 = true;

        /**
         * Method Docblock for StaticPublicMethod
         *
         * I have explicitly added a **long** description _containing_ some markup to:
         *
         * * demonstrate what it looks like
         * * test the markdown conversion process
         *
         * @param array $argument_a test argument
         * @param FooBarClass $argument_b test argument
         *
         * @return void
         */
        public static function StaticPublicMethod(array $argument_a, FooBarClass $argument_b = null)
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

    class FooBarClass2 extends SingleClass
    {
        const TEST = 'test2';
    }
}
