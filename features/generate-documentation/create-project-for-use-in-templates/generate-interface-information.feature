Feature: Generate information about one or more interfaces in a project
  In order to present information on interfaces in a project
  As a template author
  I need to have all details concerning an interface

  Background:
    Given a file named "test.php" with:
      """
      <?php
      namespace MySpace;

      /**
       * This is a summary.
       * This is a description.
       * @package Test
       * @version 1.0
       */
      interface A extends IB, IC, \DateTimeInterface {
        const CONSTANT = 1;
        public function method() {}
      }

      interface B { }
      interface C { }
      """
    When I run "phpdoc -f test.php"

  Scenario: Interface is present in the project
    Then the AST has a "interface" at expression "project.getFiles()['test.php'].getInterfaces()['\\MySpace\\A']"
    Then the AST has a "interface" at expression "project.getFiles()['test.php'].getInterfaces()['\\MySpace\\B']"
    Then the AST has a "interface" at expression "project.getFiles()['test.php'].getInterfaces()['\\MySpace\\C']"

  Scenario: Interface has a summary
    Then the AST has an expression "project.getFiles()['test.php'].getInterfaces()['\\MySpace\\A'].getSummary()" with value: "This is a summary."

  Scenario: Interface has a description
    Then the AST has an expression "project.getFiles()['test.php'].getInterfaces()['\\MySpace\\A'].getDescription()" with value: "This is a description."

  Scenario: Interface has a constant
    Then the AST has a "constant" at expression "project.getFiles()['test.php'].getInterfaces()['\\MySpace\\A'].getConstants()['CONSTANT']"

  Scenario: Interface has a method
    Then the AST has a "method" at expression "project.getFiles()['test.php'].getInterfaces()['\\MySpace\\A'].getMethods()['method']"

  Scenario: Interface correctly links to super interfaces
    Then the AST has a "interface" at expression "project.getFiles()['test.php'].getInterfaces()['\\MySpace\\A'].getParent()['\\MySpace\B']"
    And the AST has a "interface" at expression "project.getFiles()['test.php'].getInterfaces()['\\MySpace\\A'].getParent()['\\MySpace\C']"
    And the AST has an expression "project.getFiles()['test.php'].getInterfaces()['\\MySpace\\A'].getParent()[2]" with value: "\DateTime"

  Scenario: Interface correctly links to namespace
    Then the AST has a "namespace" at expression "project.getFiles()['test.php'].getInterfaces()['\\MySpace\\A'].getNamespace()"
    And the AST has an expression "project.getFiles()['test.php'].getInterfaces()['\\MySpace\\A'].getNamespace().getName()" with value: "MySpace"

  Scenario: Interface correctly links to the containing file
    Then the AST has a "file" at expression "project.getFiles()['test.php'].getInterfaces()['\\MySpace\\A'].getFile()"
    And the AST has an expression "project.getFiles()['test.php'].getInterfaces()['\\MySpace\\A'].getFile().getName()" with value: "test.php"
