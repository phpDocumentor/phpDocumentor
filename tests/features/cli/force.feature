# Currently force is the default method of execution in the BaseContext; pay extra attention to this when fixing this
# test. Without this force it somehow re-uses caches from every previous run; we may need to change how we approach this
@wip
Feature: Force parsing project files
  In order to force parsing the project
  As a developer
  I need to be able to make phpdocumentor ignore existing cache so I'm sure all changes are correctly updated

  Scenario: See normal execution doesn't force parsing
    Given A single file named "test.php" based on "NoPackagePrecedesClass.php"
    And I ran "phpdoc -f test.php"
    When I run "phpdoc -f test.php"
    Then the application must have run successfully
    And output doesn't contain "Parsing test.php"

  Scenario: See force will trigger the parser to ignore cache
    Given A single file named "test.php" based on "NoPackagePrecedesClass.php"
    And I ran "phpdoc -f test.php"
    When I run "phpdoc --force -f test.php"
    Then the application must have run successfully
    And output contains "Parsing test.php"
