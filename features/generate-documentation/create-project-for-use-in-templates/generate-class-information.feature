Feature: Generate information about one or more classes in a project
  In order to present information on classes in a project
  As a template author
  I need to have all details concerning a class

  Background:
    Given a file named "test.php" with:
      """
      <?php
      namespace MySpace;

      trait TA {}
      interface IA { }
      interface IB { }

      /**
       * This is a class summary.
       * This is a class description.
       * @package Test
       * @version 1.0
       */
      class A extends B implements IA, IB, \DateTimeInterface {
        use TA;
        const CONSTANT = 1;
        public $property;
        public function method() {}
      }

      class B extends \DateTime {}
      """
    When I run "phpdoc -f test.php"

  Scenario: Class is present in the project
    Then the AST has a "class" at expression "project.getFiles()['test.php'].getClasses()['\\MySpace\\A']"
    Then the AST has a "class" at expression "project.getFiles()['test.php'].getClasses()['\\MySpace\\B']"

  Scenario: Class has a summary
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\MySpace\\A'].getSummary()" with value: "This is a class summary."

  Scenario: Class has a description
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\MySpace\\A'].getDescription()" with value: "This is a class description."

  Scenario: Class has a constant
    Then the AST has a "constant" at expression "project.getFiles()['test.php'].getClasses()['\\MySpace\\A'].getConstants()['CONSTANT']"

  Scenario: Class has a property
    Then the AST has a "property" at expression "project.getFiles()['test.php'].getClasses()['\\MySpace\\A'].getProperties()['property']"

  Scenario: Class has a method
    Then the AST has a "method" at expression "project.getFiles()['test.php'].getClasses()['\\MySpace\\A'].getMethods()['method']"

  Scenario: Class correctly links to implemented interfaces
    Then the AST has an "interface" at expression "project.getFiles()['test.php'].getClasses()['\\MySpace\\A'].getInterfaces()['\\MySpace\\IA']"
    And the AST has an "interface" at expression "project.getFiles()['test.php'].getClasses()['\\MySpace\\A'].getInterfaces()['\\MySpace\\IB']"
    And the AST has an expression "project.getFiles()['test.php'].getClasses()['\\MySpace\\A'].getInterfaces()['\\DateTimeInterface']" with value: "\DateTimeInterface"

  Scenario: Class correctly links to super classes
    Then the AST has a "class" at expression "project.getFiles()['test.php'].getClasses()['\\MySpace\\A'].getParent()"
    And the AST has an expression "project.getFiles()['test.php'].getClasses()['\\MySpace\\A'].getParent().getName()" with value: "B"
    And the AST has an expression "project.getFiles()['test.php'].getClasses()['\\MySpace\\B'].getParent()" with value: "\DateTime"

  Scenario: Class correctly links to traits
    Then the AST has a "trait" at expression "project.getFiles()['test.php'].getClasses()['\\MySpace\\A'].getUsedTraits()[0]"
    And the AST has an expression "project.getFiles()['test.php'].getClasses()['\\MySpace\\A'].getUsedTraits()[0].getName()" with value: "TA"

  Scenario: Class correctly links to namespace
    Then the AST has a "namespace" at expression "project.getFiles()['test.php'].getClasses()['\\MySpace\\A'].getNamespace()"
    And the AST has an expression "project.getFiles()['test.php'].getClasses()['\\MySpace\\A'].getNamespace().getName()" with value: "MySpace"

  Scenario: Class correctly links to the containing file
    Then the AST has a "file" at expression "project.getFiles()['test.php'].getClasses()['\\MySpace\\A'].getFile()"
    And the AST has an expression "project.getFiles()['test.php'].getClasses()['\\MySpace\\A'].getFile().getName()" with value: "test.php"
