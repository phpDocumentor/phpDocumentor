Feature: Include directories with parsing
  As a user
  I want to be able to pass an argument to phpdocumentor to specify which directory should be processed

  Scenario: Single directory
    Given A project named "directory123" based on "ignore"
    When I run "phpdoc --directory=src --force"
    Then 2 files should be parsed
