Feature: Phpdocumentor is able to process variadic parameters

  @php5.6+
  Scenario:
    Given A single file named "test.php" based on "syntax/variadic.php"
    When I run "phpdoc -f test.php"
    Then the application must have run successfully
    And the AST has a class named "A" in file "test.php"
    And class "\A" has a method "b" with argument "d" is variadic
