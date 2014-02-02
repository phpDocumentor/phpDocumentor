Feature: Interfaces inherit information from parent interfaces

  Background:
    Given a file named "test.php" with:
      """
      <?php
      /**
       * This is an interface summary.
       *
       * This is an interface description.
       *
       * @version 1.0
       * @author Mike van Riel
       * @copyright Copyright
       */
      interface IA {
      }

      interface IB extends IA {
      }

      interface IC extends IB {
      }
      """
    When I run "phpdoc -f test.php"

  Scenario: Inherit summary from a parent interface
    Then the AST has an expression "project.getFiles()['test.php'].getInterfaces()['\\IB'].getSummary()" with value:
      """
      This is an interface summary.
      """
    And the AST has an expression "project.getFiles()['test.php'].getInterfaces()['\\IC'].getSummary()" with value:
      """
      This is an interface summary.
      """

  Scenario: Inherit description from a parent interface
    Then the AST has an expression "project.getFiles()['test.php'].getInterfaces()['\\IB'].getDescription()" with value:
      """
      This is an interface description.
      """
    Then the AST has an expression "project.getFiles()['test.php'].getInterfaces()['\\IC'].getDescription()" with value:
      """
      This is an interface description.
      """

  Scenario: Inherit description from a parent interface when interface extends two other interfaces
  Scenario: Current description is augmented with that of parent class when using {@inheritdoc}
  Scenario: Inherit constants from a parent interface
  Scenario: Inherit methods from a parent interface
  Scenario: Inherit @package tags from a parent interface
  Scenario: Inherit @subpackage tags from a parent interface if @package is equal
  Scenario: Inherit @author tags from a parent interface
  Scenario: Inherit @copyright tags from a parent interface
  Scenario: Inherit @version tags from a parent interface
