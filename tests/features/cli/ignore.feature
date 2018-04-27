Feature: Ignore files and directories
  In order to exclude files from parsing
  As a user
  I must be able to exclude certain directories and files from parsing.

  Scenario: exclude a directory using the --ignore parameter
    Given A project named "test" based on "ignore"
    When I run "phpdoc -d test --ignore test/test"
    Then the AST has a class named "Ignored" in file "test/src/Ignored.php"
    And the AST has a class named "NotIgnored" in file "test/src/NotIgnored.php"
    And the AST doesn't have a class "IgnoredTest"

  Scenario: exclude a file using the --ignore parameter
    Given A project named "test" based on "ignore"
    When I run "phpdoc --force -d test -i test/src/Ignored.php"
    Then the AST has a class named "IgnoredTest" in file "test/test/IgnoredTest.php"
    And the AST has a class named "NotIgnored" in file "test/src/NotIgnored.php"
    And the AST doesn't have a class "Ignored"

  Scenario: exclude a multiple files using the --ignore parameter
    Given A project named "test" based on "ignore"
    When I run "phpdoc --force -d test -i test/src/Ignored.php -i test/test/IgnoredTest.php"
    Then the AST has a class named "NotIgnored" in file "test/src/NotIgnored.php"
    And the AST doesn't have a class "Ignored"
    And the AST doesn't have a class "IgnoredTest"
