Feature: `@var` should link to types

  Background:
    Given a file named "test.php" with:
    """
      <?php
      class A {}

      class B {
          /** @var A */
          public $a;

          /** @var A[] */
          public $b;

          /** @var A|A[] */
          public $c;

          /** @var string */
          public $d;

          /** @var C */
          public $e;
      }
      """
    When I run "phpdoc -f test.php"

  Scenario:
    Then the AST has a "class" at expression "project.getFiles()['test.php'].getClasses()['\\B'].getProperties()['a'].getVar()[0].getTypes()[0]"

  Scenario:
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getProperties()['d'].getVar()[0].getTypes()[0]" with value: "string"

  Scenario:
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getProperties()['e'].getVar()[0].getTypes()[0]" with value: "\C"

  Scenario:
    Then the AST has a "Type\Collection" at expression "project.getFiles()['test.php'].getClasses()['\\B'].getProperties()['b'].getVar()[0].getTypes()[0]"
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getProperties()['b'].getVar()[0].getTypes()[0].getName()" with value: "array"
    Then the AST has a "class" at expression "project.getFiles()['test.php'].getClasses()['\\B'].getProperties()['b'].getVar()[0].getTypes()[0].getTypes()[0]"
    Then the AST has an expression "project.getFiles()['test.php'].getClasses()['\\B'].getProperties()['b'].getVar()[0].getTypes()[0].getKeyTypes()[0]" with value: "mixed"

  Scenario:
    Then the AST has a "class" at expression "project.getFiles()['test.php'].getClasses()['\\B'].getProperties()['c'].getVar()[0].getTypes()[0]"
    Then the AST has a "Type\Collection" at expression "project.getFiles()['test.php'].getClasses()['\\B'].getProperties()['c'].getVar()[0].getTypes()[1]"

