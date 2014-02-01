Feature: Properties inherit information from properties, with the same name, in a parent class

  Background:
    Given a file named "test.php" with:
      """
      <?php
      class A {
        /**
         * This is a constant summary.
         *
         * This is a constant description.
         *
         * @var \SimpleXMLElement This is the description.
         * @version 1.0
         * @author Mike van Riel
         * @copyright Copyright
         */
        const CONSTANT = 1;
      }

      class B extends A {
        const CONSTANT = 2;
      }

      class C extends B {
        const CONSTANT = 3;
      }

      class D extends A {
        /**
         * This is an overridden constant summary.
         *
         * This is a description specific to this class.
         * {@inheritDoc}
         */
        const CONSTANT = 4;
      }
      """

  Scenario: Constants inherit the Summary
    When I run "phpdoc -f test.php"
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getConstants()['CONSTANT'].getSummary()" with value:
      """
      This is a constant summary.
      """
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\C'].getConstants()['CONSTANT'].getSummary()" with value:
      """
      This is a constant summary.
      """

  Scenario: Inherit description from a parent constant
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getConstants()['CONSTANT'].getDescription()" with value:
      """
      This is a constant description.
      """
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\C'].getConstants()['CONSTANT'].getDescription()" with value:
      """
      This is a constant description.
      """

  Scenario: Current description is augmented with that of parent class when using {@inheritdoc}
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\D'].getConstants()['CONSTANT'].getDescription()" with value:
      """
      This is a description specific to this class.
      This is a constant description.
      """

  Scenario: Inherit @var tags from a parent constant
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getConstants()['CONSTANT'].getVar()[0].getTypes()[0]" with value:
      """
      \SimpleXMLElement
      """
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getConstants()['CONSTANT'].getVar()[0].getDescription()" with value:
      """
      This is the description.
      """
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getConstants()['CONSTANT'].getVar()[0].getTypes()[0]" with value:
      """
      \SimpleXMLElement
      """
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\C'].getConstants()['CONSTANT'].getVar()[0].getDescription()" with value:
      """
      This is the description.
      """

  Scenario: Inherit @author tags from a parent constant
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getConstants()['CONSTANT'].getAuthor()[0].getDescription()" with value:
      """
      Mike van Riel
      """
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\C'].getConstants()['CONSTANT'].getAuthor()[0].getDescription()" with value:
      """
      Mike van Riel
      """

  Scenario: Inherit @copyright tags from a parent constant
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getConstants()['CONSTANT'].getCopyright()[0].getDescription()" with value:
      """
      Copyright
      """
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\C'].getConstants()['CONSTANT'].getCopyright()[0].getDescription()" with value:
      """
      Copyright
      """

  Scenario: Inherit @version tags from a parent constant
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getConstants()['CONSTANT'].getVersion()[0].getVersion()" with value:
      """
      1.0
      """
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\C'].getConstants()['CONSTANT'].getVersion()[0].getVersion()" with value:
      """
      1.0
      """

