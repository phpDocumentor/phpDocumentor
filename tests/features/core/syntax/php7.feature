Feature: Phpdocumentor is able to process php 7+ syntax

  @php7.0+
  Scenario: phpdocumentor is able to process array contants
    Given A single file named "test.php" based on "syntax/constarray.php"
    When I run "phpdoc -f test.php"
    Then the application must have run successfully
    And the AST has a class named "A" in file "test.php"
    And class "\A" has a constant "CONFIG_CONFASSISTANT"

  @php7.0+
  Scenario: phpdocumentor is able to process grouped use and resolves the types correctly
    Given A single file named "test.php" based on "syntax/groupedUse.php"
    When I run "phpdoc -f test.php"
    Then the application must have run successfully
    And the AST has a class named "A" in file "test.php"
    And class "\phpDocumentor\A" has a method someMethodWithDocblock with argument a of type "\phpDocumentor\ClassA"
    And class "\phpDocumentor\A" has a method someMethodWithDocblock with param a of type "\phpDocumentor\ClassA"

  @php7.0+ @github-1777
  Scenario: phpdocumentor is able to process anonymous class in object method
    Given A single file named "test.php" based on "syntax/AnonymousClassInObjectMethod.php"
    When I run "phpdoc -f test.php"
    Then the application must have run successfully
    And the AST has a class named "TTT" in file "test.php"
