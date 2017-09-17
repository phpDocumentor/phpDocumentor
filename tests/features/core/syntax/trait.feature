Feature: Phpdocumentor is able to process traits

  Scenario:
    Given A single file named "test.php" based on "syntax/trait.php"
    When I run "phpdoc -f test.php"
    Then the application must have run successfully
    And the AST has a trait named "MyTrait" in file "test.php"
