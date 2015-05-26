Feature: issue a validation error is there is an @subpackage tag but no @package
  As an author
  I want to be warned when the @package is missing when using @subpackage
  So that I can fix this erroneous state

Scenario:
    Given a file named "test.php" with:
      """
      <?php
      /**
       * File-level Docblock
       */

      /**
       * This is a summary.
       * @package My\Package
       * @subpackage Subpackage
       */
      class A {}
      """
    When I run "phpdoc -f test.php"
    Then the AST has an expression "project.getFiles()['test.php'].getAllErrors().count()" with value: "0"


  Scenario:
    Given a file named "test.php" with:
      """
      <?php
      /**
       * File-level Docblock
       */

      /**
       * This is a summary.
       * @subpackage Subpackage
       */
      class A {}
      """
    When I run "phpdoc -f test.php"
    Then the AST has an expression "project.getFiles()['test.php'].getAllErrors().count()" with value: "1"
    Then the AST has an expression "project.getFiles()['test.php'].getAllErrors()[0].getCode()" with value:
      """
      PPC:ERR-50004
      """
