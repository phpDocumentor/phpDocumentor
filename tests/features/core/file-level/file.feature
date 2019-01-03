Feature: Parsing a php file
  In order to document all php files
  As a developer
  I want to be able to define a docblock containing a license in a file.

  @issue @github-1915
  Scenario: File only contains require statements
    Given A single file named "test.php" based on "requireOnly.php"
    When I run "phpdoc -f test.php"
    Then the application must have run successfully
    And the ast has a file named "test.php" with a summary:
    """
    This file is part of phpDocumentor.
    """

  @issue @github-1978
  Scenario: File only contains a function
    Given A single file named "test.php" based on "functionOnly.php"
    When I run "phpdoc -f test.php"
    Then the application must have run successfully
    And the ast has a function named "\foo"
    And the namespace '\' has a function named 'foo'

  @issue @github-604
  Scenario: File only contains a file level docblock
    Given A single file named "test.php" based on "emptyFile.php"
    When I run "phpdoc -f test.php"
    And the ast has a file named "test.php" with a summary:
    """
    This file is part of phpDocumentor.
    """git
