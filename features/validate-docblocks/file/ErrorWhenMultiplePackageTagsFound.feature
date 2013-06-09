Feature: Validate whether the File-level DocBlock is valid
  In order to have correctly interpreted source code and apply clean coding
  As a documentation generating user
  I want to see if there are any errors specifically regarding my File-Level DocBlocks

  Scenario: Show an error when a DocBlock has multiple @package tags.
    Given I am in the phpDocumentor root directory
    When I run phpDocumentor against the file "tests/data/MultiplePackagesDocBlock.php"
    Then I should get a log entry "Only one @package tag is allowed"
