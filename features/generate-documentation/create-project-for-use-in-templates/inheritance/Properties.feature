Feature: Properties inherit information from properties, with the same name, in a parent class

  Background:
    Given a file named "test.php" with:
      """
      <?php
      class A {
        /**
         * This is a property summary.
         *
         * This is a property description.
         *
         * @var \SimpleXMLElement This is the description.
         * @author Mike van Riel
         * @copyright Copyright
         * @version 1.0
         */
        public $property;
      }

      class B extends A {
        public $property;
      }

      class C extends B {
        public $property;
      }

      class D extends A {
        /**
         * This is an overridden property summary.
         *
         * This is a description specific to this class.
         * {@inheritDoc}
         */
        public $property;
      }
      """
    When I run "phpdoc -f test.php"

  Scenario: Properties inherit the Summary
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getProperties()['property'].getSummary()" with value:
      """
      This is a property summary.
      """
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\C'].getProperties()['property'].getSummary()" with value:
      """
      This is a property summary.
      """

  Scenario: Inherit description from a parent property
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getProperties()['property'].getDescription()" with value:
      """
      This is a property description.
      """
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\C'].getProperties()['property'].getDescription()" with value:
      """
      This is a property description.
      """

  Scenario: Current description is augmented with that of parent class when using {@inheritdoc}
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\D'].getProperties()['property'].getDescription()" with value:
      """
      This is a description specific to this class.
      This is a property description.
      """


  Scenario: Inherit @var tags from a parent property
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getProperties()['property'].getVar()[0].getTypes()[0]" with value:
      """
      \SimpleXMLElement
      """
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getProperties()['property'].getVar()[0].getDescription()" with value:
      """
      This is the description.
      """
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getProperties()['property'].getVar()[0].getTypes()[0]" with value:
      """
      \SimpleXMLElement
      """
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\C'].getProperties()['property'].getVar()[0].getDescription()" with value:
      """
      This is the description.
      """

  Scenario: Inherit @author tags from a parent property
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getProperties()['property'].getAuthor()[0].getDescription()" with value:
      """
      Mike van Riel
      """
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\C'].getProperties()['property'].getAuthor()[0].getDescription()" with value:
      """
      Mike van Riel
      """

  Scenario: Inherit @copyright tags from a parent property
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getProperties()['property'].getCopyright()[0].getDescription()" with value:
      """
      Copyright
      """
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\C'].getProperties()['property'].getCopyright()[0].getDescription()" with value:
      """
      Copyright
      """

  Scenario: Inherit @version tags from a parent property
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getProperties()['property'].getVersion()[0].getVersion()" with value:
      """
      1.0
      """
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\C'].getProperties()['property'].getVersion()[0].getVersion()" with value:
      """
      1.0
      """

