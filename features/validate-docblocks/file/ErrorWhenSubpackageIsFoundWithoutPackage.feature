Feature: Validate whether the File-level DocBlock is valid
  In order to have correctly interpreted source code and apply clean coding
  As a documentation generating user
  I want to see if there are any errors specifically regarding my File-Level DocBlocks

  Scenario: Show an error when a DocBlock has a @subpackage but no @package tag.
    Given I am in the phpDocumentor root directory
    When I run phpDocumentor against the file "tests/data/NoPackageDocBlock.php"
    Then I should get a log entry "Cannot have a @subpackage when a @package tag is not present"

