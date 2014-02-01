Feature: Interfaces inherit information from parent interfaces

  Background:
    Given a file named "test.php" with:
      """
      <?php
      /** This is an interface summary */
      interface IA {
        public function method() {}
      }

      interface IB extends IA {
        public function method() {}
      }

      interface IC extends IA {
        public function method() {}
      }

      /** This is a class summary */
      class A {
        /** This is a property summary */
        public $property;

        /** This is a method summary */
        public function method() {}
      }

      class B extends A {
        public $property;
        public function method() {}
      }

      class C extends B {
        public $property;
        public function method() {}
      }
      """

  Scenario: Inherit summary from a parent interface
    When I run "phpdoc -f test.php"
    Then the AST has an expression "project.getFiles()['test.php'].getInterfaces()['\\IB'].getSummary()" with value:
      """
      This is a interface summary
      """
    And the AST has an expression "project.getFiles()['test.php'].getInterfaces()['\\C'].getSummary()" with value:
      """
      This is a interface summary
      """

  Scenario: Inherit description from a parent interface
  Scenario: Inherit description from a parent interface when interface extends two other interfaces
  Scenario: Current description is augmented with that of parent class when using {@inheritdoc}
  Scenario: Inherit constants from a parent interface
  Scenario: Inherit methods from a parent interface
  Scenario: Inherit @package tags from a parent interface
  Scenario: Inherit @subpackage tags from a parent interface if @package is equal
  Scenario: Inherit @author tags from a parent interface
  Scenario: Inherit @copyright tags from a parent interface
  Scenario: Inherit @version tags from a parent interface
