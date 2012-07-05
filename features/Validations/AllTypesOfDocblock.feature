Feature: Validate whether my DocBlocks are valid
  In order to have correctly interpreted source code and apply clean coding
  As a documentation generating user
  I want to see if there are any errors regarding any of my DocBlocks

  Scenario: Show an error when DocBlocks are missing a Short Description.
    Given I am in the phpDocumentor root directory
    When I run phpDocumentor against the file "tests/data/NoShortDescription.php"
    Then I should get a log entry "No short description for function \noShortDescriptionFunction()"
    Then I should get a log entry "No short description for file NoShortDescription.php"
    Then I should get a log entry "No short description for class \NoShortDescription"
    Then I should get a log entry "No short description for property $_noShortDescriptionProperty"
    Then I should get a log entry "No short description for method noShortDescriptionFound()"