Feature: Generate information about one or more traits in a project
  In order to present information on traits in a project
  As a template author
  I need to have all details concerning a trait

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
      trait A {
        protected $property;
        public function method() {}
      }

      """
    When I run "phpdoc -f test.php"

  Scenario: Trait is present in the project
    Then the AST has a "trait" at expression "project.getFiles()['test.php'].getTraits()['\\MySpace\\A']"

  Scenario: Trait has a summary
    Then the AST has an expression "project.getFiles()['test.php'].getTraits()['\\MySpace\\A'].getSummary()" with value: "This is a summary."

  Scenario: Trait has a description
    Then the AST has an expression "project.getFiles()['test.php'].getTraits()['\\MySpace\\A'].getDescription()" with value: "This is a description."

  Scenario: Trait has a property
    Then the AST has a "property" at expression "project.getFiles()['test.php'].getTraits()['\\MySpace\\A'].getProperties()['property']"

  Scenario: Trait has a method
    Then the AST has a "method" at expression "project.getFiles()['test.php'].getTraits()['\\MySpace\\A'].getMethods()['method']"

  Scenario: Trait correctly links to namespace
    Then the AST has a "namespace" at expression "project.getFiles()['test.php'].getTraits()['\\MySpace\\A'].getNamespace()"
    And the AST has an expression "project.getFiles()['test.php'].getTraits()['\\MySpace\\A'].getNamespace().getName()" with value: "MySpace"

  Scenario: Trait correctly links to the containing file
    Then the AST has a "file" at expression "project.getFiles()['test.php'].getTraits()['\\MySpace\\A'].getFile()"
    And the AST has an expression "project.getFiles()['test.php'].getTraits()['\\MySpace\\A'].getFile().getName()" with value: "test.php"
