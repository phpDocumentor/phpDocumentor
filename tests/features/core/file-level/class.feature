Feature: Parsing a php file with a class in it
  In order to document all php files
  As a developer
  I want to be able to capture the elements associated with a class

  Scenario: File contains class
    Given A single file named "test.php" based on "class.php"
    When I run "phpdoc -f test.php"
    Then the application must have run successfully
    And the AST has a class named "Def" in file "test.php"
    And class "\Abc\Def" has a method "Ghi"
    And class "\Abc\Def" has a method "Jkl"
    And class "\Abc\Def" has a method "Mno"
    And class "\Abc\Def" has a property "ghij"
    And class "\Abc\Def" has a property "klmn"
    And class "\Abc\Def" has a property "opqr"
