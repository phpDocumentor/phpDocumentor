Feature: Generate an AST (Structure file)
  In order to generate an Abstract Syntax Tree of the source code
  as documentation generating user
  I need to parse the contents of my source tree

  Scenario: Do not provide any files or directories to parse
    Given I am in the phpDocumentor root directory
    When I run phpDocumentor against no files or directories
    Then the exit code should be non-zero
    And I should get an exception containing "No parsable files were found"

  Scenario: Generate a AST from an empty file
    Given I am in the phpDocumentor root directory
    When I run phpDocumentor against the file "tests/data/EmptyFile.php"
    Then my AST should contain the file "EmptyFile.php"
    But my AST should contain 0 class definitions

  Scenario: Do not output anything to STDOUT in quiet mode
    Given I am in the phpDocumentor root directory
    When I run phpDocumentor against the file "tests/data/DocBlockTestFixture.php" using option "-q"
    Then there should be no output
