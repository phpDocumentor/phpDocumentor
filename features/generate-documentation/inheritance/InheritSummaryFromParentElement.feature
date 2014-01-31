Feature:
  In order to simplify documentation authoring
  As an author
  I need summaries in DocBlocks to inherit from the DocBlock of a parent element

  Background:
    Given a file named "test.php" with:
      """
      <?php
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

  Scenario:
    When I run "phpdoc -f test.php"
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getSummary()" with value:
      """
      This is a class summary
      """
    And the AST has an expression "project.getFiles()['test.php'].getClasses()['\\C'].getSummary()" with value:
      """
      This is a class summary
      """
