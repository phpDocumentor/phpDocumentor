Feature:

  Scenario: Do not output anything to STDOUT in quiet mode
    Given I am in the phpDocumentor root directory
    When I run phpDocumentor against the file "tests/data/DocBlockTestFixture.php" using option "-q"
    Then there should be no output
