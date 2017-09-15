Feature: Phpdocumentor is able to process varidict parameters

  @php7.0+
  Scenario:
    Given A single file named "test.php" based on "syntax/constarray.php"
    When I run "phpdoc -f test.php"
    Then the application must have run successfully
    And the AST has a class named "A" in file "test.php"
    And class "\A" has a constant "CONFIG_CONFASSISTANT"
