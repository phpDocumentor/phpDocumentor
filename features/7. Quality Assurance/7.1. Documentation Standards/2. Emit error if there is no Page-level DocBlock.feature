Feature: Validate whether the File-level DocBlock is valid
  In order to have correctly interpreted source code and apply clean coding
  As a documentation generating user
  I want to see if there are any errors specifically regarding my File-Level DocBlocks

  Scenario: Parse successfully when a DocBlock is first in a file without a
  package tag and directly preceeds a class element that has a
  package tag
    Given I am in the phpDocumentor root directory
    When I run phpDocumentor against the file "tests/data/file-level/NoPackagePrecedesDocBlock.php"
    Then I should not get a log entry "No page-level DocBlock was found in file NoPackagePrecedesDocBlock.php"

  Scenario: Parse successfully when a DocBlock is first in a file with a
  package tag and directly preceeds a class element that has a
  package tag as well
    Given I am in the phpDocumentor root directory
    When I run phpDocumentor against the file "tests/data/file-level/PackagePrecedesDocBlock.php"
    Then I should not get a log entry "No page-level DocBlock was found in file PackagePrecedesDocBlock.php"

  Scenario: The first DocBlock in a file that is succeeded by a non-documentable
  element should be the File-level DocBlock and should not throw an
  warning.
    Given I am in the phpDocumentor root directory
    When I run phpDocumentor with:
    """
    <?php
    /**
     * Short description for this file
     */
    namespace My\Name\Space {}
    """
    Then I should not get a log entry containing "No page-level DocBlock was found"
