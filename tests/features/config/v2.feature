Feature: phpdocumentor should be able to use v2 format config file.
  To make the step to phpdoc v3 as small as possible we want to be backwards compatible in the configuration
  of phpdocumentor.

  Background:
    Given A project named "v2" based on "simple"
    And configuration file based on "v2Target.xml"
    When I run "phpdoc"
    Then the application must have run successfully

  Scenario: User has provided a custom target directory in the config
    Then documentation should be found in "build/api/docs"

  Scenario: User defined files are read
    Then the ast contains the files:
      | src/Post.php |
