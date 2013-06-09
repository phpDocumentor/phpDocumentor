Feature: Validate whether a Function's DocBlock is valid
  In order to have correctly interpreted source code and apply clean coding
  As a documentation generating user
  I want to see if there are any errors specifically regarding my DocBlocks for global functions

  Scenario: Show an error when a DocBlock is missing for a function.
    Given I am in the phpDocumentor root directory
    When I run phpDocumentor against the file "tests/data/NoFunctionDocBlock.php"
    Then I should get a log entry "No DocBlock was found for \noFunctionDocBlock()"
