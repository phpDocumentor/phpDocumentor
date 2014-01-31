Feature:
  In order to simplify documentation authoring
  As an author
  I need summaries in DocBlocks to inherit from the DocBlock of a parent element

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

  Scenario: Classes inherit the Summary
    When I run "phpdoc -f test.php"
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getSummary()" with value:
      """
      This is a class summary
      """
    And the AST has an expression "project.getFiles()['test.php'].getClasses()['\\C'].getSummary()" with value:
      """
      This is a class summary
      """

  Scenario: Interfaces inherit the Summary
    When I run "phpdoc -f test.php"
    Then the AST has an expression "project.getFiles()['test.php'].getInterfaces()['\\IB'].getSummary()" with value:
      """
      This is a interface summary
      """
    And the AST has an expression "project.getFiles()['test.php'].getInterfaces()['\\C'].getSummary()" with value:
      """
      This is a interface summary
      """

  Scenario: Class methods inherit the Summary
    When I run "phpdoc -f test.php"
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getMethods()['method'].getSummary()" with value:
      """
      This is a method summary
      """
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\C'].getMethods()['method'].getSummary()" with value:
      """
      This is a method summary
      """

  Scenario: Interface methods inherit the Summary
    When I run "phpdoc -f test.php"
    Then the AST has an expression "project.getFiles()['test.php'].getInterfaces()['\\IB'].getMethods()['method'].getSummary()" with value:
      """
      This is a method summary
      """
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\IC'].getMethods()['method'].getSummary()" with value:
      """
      This is a method summary
      """

  Scenario: Properties inherit the Summary
    When I run "phpdoc -f test.php"
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getProperties()['property'].getSummary()" with value:
      """
      This is a property summary
      """
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\C'].getProperties()['property'].getSummary()" with value:
      """
      This is a property summary
      """
