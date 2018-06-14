Feature: Include directories with parsing
  As a user
  I want to be able to pass an argument to phpdocumentor to specify which directory should be processed

  Scenario: Single directory
  Given A project named "directory" based on "ignore"
  When I run "phpdoc --directory=directory/src"
  Then 2 files should be parsed
