<?php
/**
 * @package File
 * @filesource
 */

namespace My\Space {
    define("GLOBAL_CONSTANT_DEFINE", 'test');

    /**
     * @package Constant
     */
    define("Namespaced\\GLOBAL_CONSTANT_DEFINE", 'test');

    /**
     * @package Constant\Specific
     */
    const GLOBAL_CONSTANT_CONST = 'test';

    /**
     *
     *
     * @param integer   $param1 Example of a description & with ampersand (&).
     * @param \stdClass $param2
     * @param string    $param3
     *
     * @return void
     */
    function globalFunction($param1, \stdClass $param2, $param3 = '')
    {

    }

    /**
     * A reference implementation for a subclass.
     *
     * This subclass's package has a different case than the superclass to test issue #558.
     *
     * @package class
     */
    class SubClass extends SuperClass
    {

        /**
         * @var integer   $propertyListItem1 The first property in a list
         * @var \stdClass $propertyListItem2 The second property in a list
         */
        public $propertyListItem1, $propertyListItem2;
    }

    /**
     * This is the summary for this class.
     *
     * This is a *longer* description that may contain Markdown code
     * and span multiple lines without causing any issues.
     *
     * @source
     * @author Mike van Riel <mike.vanriel@naenius.com>
     * @package Class
     * @method string myMagicMethod(\stdClass $argument1) This is a description.
     * @property string $myMagicProperty This is a description.
     */
    class SuperClass implements SubInterface
    {
        const CLASS_CONSTANT = 'test';

        /** @var integer $staticProperty A static property */
        public static $staticProperty;

        /**
         * A public property
         * @var SubClass
         * @deprecated
         */
        public $publicProperty;

        protected $protectedProperty;

        private $privateProperty;

        /**
         * This is a public method.
         *
         * Example:
         *
         * ```
         * <?php
         * echo (string)$class->publicMethod();
         * if (true) {
         *     echo 'indented string';
         * }
         * ```
         *
         * @deprecated This is superseded by the SuperClass
         *
         * @see GLOBAL_CONSTANT_DEFINE       Refer to global constant.
         * @see globalFunction()             Refer to global function.
         * @see SuperClass::CLASS_CONSTANT   Refer to class constant.
         * @see self::CLASS_CONSTANT         Refer to class constant.
         * @see self::staticMethod()         Refer to method.
         * @see self::$privateProperty       Refer to property.
         * @see SuperClass::$privateProperty Refer to property.
         * @see SuperInterface               Refer to interface.
         * @see SubClass                     Refer to class.
         *
         * @throws \Exception if it fails.
         * @uses self::protectedMethod()
         * @link http://www.phpdoc.org
         *
         * @return SubInterface an instance of the SubInterface interface.
         * Example in the return statement:
         * ```
         * <?php
         * if (true) {
         *     echo 'another indented string';
         * }
         * ```
         */
        public function publicMethod()
        {
        }

        static public function staticMethod()
        {
        }

        protected function protectedMethod(\stdClass $argument)
        {
        }

        private function privateMethod()
        {
        }
    }

    interface SubInterface extends SuperInterface,AnotherSuperInterface
    {

    }

    /**
     * @package File
     * @subpackage Interface\Super
     */
    interface SuperInterface
    {
        const INTERFACE_CONSTANT = 'test';

        public function publicMethod();
    }

    /**
     * @package File
     * @subpackage Interface
     */
    interface AnotherSuperInterface
    {
        static public function staticMethod();
    }
}

namespace My\Space\Errors {

    /**
     * @package Package1
     * @package Package2
     * @subpackage Subpackage1
     * @subpackage Subpackage2
     */
    class MultiplePackageTags
    {

        /**
         * @return type This is a generic default injected by IDEs, and is not a valid type
         */
        public function responseMayNotBeType()
        {

        }
    }

    /**
     * @subpackage Subpackage1
     */
    class SubPackageWithoutPackage
    {

    }
}
