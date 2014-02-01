Feature: Methods inherit information from methods, with the same name, in a parent class or implemented interface

  Background:
    Given a file named "test.php" with:
      """
      <?php
      interface IA {
        /**
         * This is a method summary.
         *
         * This is a method description.
         *
         * @author Mike van Riel
         * @copyright Copyright
         * @version 1.0
         *
         * @param \DateTime $a This is the description.
         *
         * @return string This is the description.
         */
        public function method() {}
      }

      interface IB extends IA {
        public function method() {}
      }

      interface IC extends IB {
        public function method() {}
      }

      class A {
        /**
         * This is a method summary.
         *
         * This is a method description.
         *
         * @author Mike van Riel
         * @copyright Copyright
         * @version 1.0
         *
         * @param \DateTime $a This is the description.
         *
         * @return string This is the description.
         */
        public function method($a) {}
      }

      class B extends A {
        public function method($a) {}
      }

      class C extends B {
        public function method($a) {}
      }

      class D extends A {
        /**
         * This is an overridden method summary.
         *
         * This is a description specific to this class.
         * {@inheritDoc}
         */
        public function method();
      }

      class E implements IA {
        public function method();
      }
      """
    When I run "phpdoc -f test.php"

  Scenario: Methods belonging to a class inherit the Summary
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getMethods()['method'].getSummary()" with value:
      """
      This is a method summary.
      """
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\C'].getMethods()['method'].getSummary()" with value:
      """
      This is a method summary.
      """

  Scenario: Methods belonging to an interface inherit the Summary
    Then the AST has an expression "project.getFiles()['test.php'].getInterfaces()['\\IB'].getMethods()['method'].getSummary()" with value:
      """
      This is a method summary.
      """
    Then the AST has an expression "project.getFiles()['test.php'].getInterfaces()['\\IC'].getMethods()['method'].getSummary()" with value:
      """
      This is a method summary.
      """

  Scenario: Methods inherit the Summary from an implemented parent interface's method
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\E'].getMethods()['method'].getSummary()" with value:
      """
      This is a method summary.
      """

  Scenario: Inherit description from a parent method
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getMethods()['method'].getDescription()" with value:
      """
      This is a method description.
      """
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\C'].getMethods()['method'].getDescription()" with value:
      """
      This is a method description.
      """

  Scenario: Methods belonging to an interface inherit the description
    Then the AST has an expression "project.getFiles()['test.php'].getInterfaces()['\\IB'].getMethods()['method'].getDescription()" with value:
      """
      This is a method description.
      """
    Then the AST has an expression "project.getFiles()['test.php'].getInterfaces()['\\IB'].getMethods()['method'].getDescription()" with value:
      """
      This is a method description.
      """

  Scenario: Methods inherit the description from an implemented parent interface's method
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\E'].getMethods()['method'].getDescription()" with value:
      """
      This is a method description.
      """

  Scenario: Current description is augmented with that of parent class when using {@inheritdoc}
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\D'].getMethods()['method'].getDescription()" with value:
      """
      This is a description specific to this class.
      This is a method description.
      """

  Scenario: Inherit @param tags from a parent method
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getMethods()['method'].getParam()[0].getTypes()[0]" with value:
      """
      \DateTime
      """
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getMethods()['method'].getParam()[0].getDescription()" with value:
      """
      This is the description.
      """
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getMethods()['method'].getParam()[0].getVariableName()" with value:
      """
      $a
      """
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\C'].getMethods()['method'].getParam()[0].getTypes()[0]" with value:
      """
      \DateTime
      """
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\C'].getMethods()['method'].getParam()[0].getDescription()" with value:
      """
      This is the description.
      """
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\C'].getMethods()['method'].getParam()[0].getVariableName()" with value:
      """
      $a
      """

  Scenario: Inherit @return tags from a parent method
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getMethods()['method'].getReturn()[0].getTypes()[0]" with value:
      """
      string
      """
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getMethods()['method'].getReturn()[0].getDescription()" with value:
      """
      This is the description.
      """
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\C'].getMethods()['method'].getReturn()[0].getTypes()[0]" with value:
      """
      string
      """
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\C'].getMethods()['method'].getReturn()[0].getDescription()" with value:
      """
      This is the description.
      """

  Scenario: Inherit @author tags from a parent method
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getMethods()['method'].getAuthor()[0].getDescription()" with value:
      """
      Mike van Riel
      """
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\C'].getMethods()['method'].getAuthor()[0].getDescription()" with value:
      """
      Mike van Riel
      """

  Scenario: Inherit @copyright tags from a parent method
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getMethods()['method'].getCopyright()[0].getDescription()" with value:
      """
      Copyright
      """
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\C'].getMethods()['method'].getCopyright()[0].getDescription()" with value:
      """
      Copyright
      """

  Scenario: Inherit @version tags from a parent method
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getMethods()['method'].getVersion()[0].getVersion()" with value:
      """
      1.0
      """
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\C'].getMethods()['method'].getVersion()[0].getVersion()" with value:
      """
      1.0
      """

