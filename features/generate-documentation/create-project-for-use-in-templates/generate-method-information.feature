Feature: Generate information about one or more methods in a class of a project
  In order to present information on methods in a project
  As a template author
  I need to have all details concerning a method in a class

  Background:
    Given a file named "test.php" with:
      """
      <?php
      namespace MySpace;

      class A {
        /**
         * This is a summary.
         * This is a description.
         * @package Test
         * @version 1.0
         *
         * @param int $a This is an integer.
         * @param \DateTime $b This is a DateTime object.
         * @param B $c This is an internal object that can be located.
         *
         * @return B
         */
        public function method($a, \DateTime $b, B $c) {}
      }

      class B {}

      """
    When I run "phpdoc -f test.php"

  Scenario: Method is present in the project
    Then the AST has a "method" at expression "project.getFiles()['test.php'].getClasses()['\\MySpace\\A'].getMethods()['method']"

  Scenario: Method has a summary
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\MySpace\\A'].getMethods()['method'].getSummary()" with value: "This is a summary."

  Scenario: Method has a description
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\MySpace\\A'].getMethods()['method'].getDescription()" with value: "This is a description."

  Scenario: Method has arguments
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\MySpace\\A'].getMethods()['method'].getArguments().count()" with value: "3"
    And the AST has an "argument" at expression "project.getFiles()['test.php'].getClasses()['\\MySpace\\A'].getMethods()['method'].getArguments()['$a']"
    And the AST has an "argument" at expression "project.getFiles()['test.php'].getClasses()['\\MySpace\\A'].getMethods()['method'].getArguments()['$b']"
    And the AST has an "argument" at expression "project.getFiles()['test.php'].getClasses()['\\MySpace\\A'].getMethods()['method'].getArguments()['$c']"

  Scenario: An argument has a description
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\MySpace\\A'].getMethods()['method'].getArguments()['$a'].getDescription()" with value: "This is an integer."

  Scenario: An argument has the correct type
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\MySpace\\A'].getMethods()['method'].getArguments()['$a'].getTypes()[0]" with value: "int"
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\MySpace\\A'].getMethods()['method'].getArguments()['$b'].getTypes()[0]" with value: "\DateTime"
    Then the AST has a "class" at expression "project.getFiles()['test.php'].getClasses()['\\MySpace\\A'].getMethods()['method'].getArguments()['$c'].getTypes()[0]"
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\MySpace\\A'].getMethods()['method'].getArguments()['$c'].getTypes()[0].getName()" with value: "B"

  Scenario: Method correctly links to the parent class
    Then the AST has a "class" at expression "project.getFiles()['test.php'].getClasses()['\\MySpace\\A'].getMethods()['method'].getParent()"
    And the AST has an expression "project.getFiles()['test.php'].getClasses()['\\MySpace\\A'].getMethods()['method'].getParent().getName()" with value: "A"
