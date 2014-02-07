Feature: Classes inherit information from parent classes and implemented interfaces

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

      /**
       * This is a class summary
       *
       * This is a class description.
       *
       * @version 1.0
       */
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
    When I run "phpdoc -f test.php"

  Scenario: Inherit summary from a parent class
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getSummary()" with value:
      """
      This is a class summary
      """
    And the AST has an expression "project.getFiles()['test.php'].getClasses()['\\C'].getSummary()" with value:
      """
      This is a class summary
      """

  Scenario: Inherit description from a parent class
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getDescription()" with value:
      """
      This is a class description.
      """
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\C'].getDescription()" with value:
      """
      This is a class description.
      """

  Scenario: Current description is augmented with that of parent class when using {@inheritdoc}
  Scenario: Inherit constants from a parent class
  Scenario: Inherit constants from a implemented interface
  Scenario: Inherit methods from a parent class
  Scenario: Inherit properties from a parent class
  Scenario: Inherit @method tags from a parent class
  Scenario: Inherit @property tags from a parent class
  Scenario: Inherit @property-read tags from a parent class
  Scenario: Inherit @property-write tags from a parent class
  Scenario: Inherit @package tags from a parent class
  Scenario: Inherit @subpackage tags from a parent class if @package is equal
  Scenario: Inherit @author tags from a parent class
  Scenario: Inherit @copyright tags from a parent class
  Scenario: Inherit @version tags from a parent class
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getVersion()[0].getVersion()" with value:
      """
      1.0
      """
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\C'].getVersion()[0].getVersion()" with value:
      """
      1.0
      """

