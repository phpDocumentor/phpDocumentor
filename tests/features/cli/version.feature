Feature: Phpdocumentor is able to expose it's version
  As an user
  I want to be able to get the version number of phpdocumentor

  Scenario: Passing the -V flag to phpdocumentor should print the current version to the console
    When I run "phpdoc -V"
    Then output contains "phpDocumentor v"
