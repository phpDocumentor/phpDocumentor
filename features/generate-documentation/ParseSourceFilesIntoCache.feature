Feature: Parse source files
  In order to generate documentation
  as a documentation generating user
  I need to parse the contents of my source tree

# FIXME
#  Scenario: Do not provide any files or directories to parse
#    Given I am in the phpDocumentor root directory
#    When I run phpDocumentor against no files or directories
#    Then the exit code should be non-zero
#    And I should get an exception containing "No parsable files were found"

  Scenario: Do not output anything to STDOUT in quiet mode
    Given I am in the phpDocumentor root directory
    When I run phpDocumentor against the file "tests/data/DocBlockTestFixture.php" using option "-q"
    Then there should be no output
