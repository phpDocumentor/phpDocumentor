Feature: Phpdocumentor is able to process php 7.1+ syntax

  @php7.1+
  Scenario: phpdocumentor is able to process nullable return types
    Given A single file named "test.php" based on "syntax/nullable.php"
    When I run "phpdoc -f test.php"
    Then the application must have run successfully
    And the AST has a class named "A" in file "test.php"
    And class "\A" has a method "getId"
    And class "\A" has a method "getId" with returntype '?int'

  @php7.1+ @github-1831
  Scenario: phpdocumentor recognizes  pseudo-type "iterable"
    Given A single file named "test.php" based on "syntax/7.1-pseudo-types.php"
    When I run "phpdoc -f test.php"
    Then the application must have run successfully
    And the AST has a class named "A" in file "test.php"
    And class "\A" has a method "method" with returntype 'iterable'
