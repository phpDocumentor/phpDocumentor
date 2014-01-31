Feature: Validate whether the File-level DocBlock is valid
  In order to have correctly interpreted source code and apply clean coding
  As a documentation generating user
  I want to see if there are any errors specifically regarding my File-Level DocBlocks

#  FIXME
#  Scenario: Show an error when a DocBlock has multiple @package tags.
#    Given I am in the phpDocumentor root directory
#    When I run phpDocumentor against the file "tests/data/MultiplePackagesDocBlock.php"
#    Then I should get a log entry "Only one @package tag is allowed"

#  FIXME
#  Scenario: Show an error when a DocBlock has a @subpackage but no @package tag.
#    Given I am in the phpDocumentor root directory
#    When I run phpDocumentor against the file "tests/data/NoPackageDocBlock.php"
#    Then I should get a log entry "Cannot have a @subpackage when a @package tag is not present"

#  FIXME
#  Scenario: Show an error when no File-level DocBlock is present but a Class
#            DocBlock is first
#    Given I am in the phpDocumentor root directory
#    When I run phpDocumentor against the file "tests/data/file-level/NoPackagePrecedesClass.php"
#    Then I should get a log entry "No page-level DocBlock was found in file NoPackagePrecedesClass.php"

#  FIXME
#  Scenario: Show an error when a File-level DocBlock is present, it has no
#            package tag and is not preceeding a class DocBlock
#    Given I am in the phpDocumentor root directory
#    When I run phpDocumentor against the file "tests/data/file-level/NoPackagePrecedesComment.php"
#    Then I should get a log entry "No page-level DocBlock was found in file NoPackagePrecedesComment.php"

#  FIXME
#  Scenario: Show an error when a DocBlock is first in a file but directly
#            preceeding a documentable element (i.e. define)
#    Given I am in the phpDocumentor root directory
#    When I run phpDocumentor against the file "tests/data/file-level/NoPackagePrecedesDefine.php"
#    Then I should get a log entry "No page-level DocBlock was found in file NoPackagePrecedesDefine.php"

#  FIXME
#  Scenario: Parse successfully when a DocBlock is first in a file with a
#            package tag and directly preceeds another documentable element.
#    Given I am in the phpDocumentor root directory
#    When I run phpDocumentor against the file "tests/data/file-level/PackagePrecedesDefine.php"
#    Then I should not get a log entry "No page-level DocBlock was found in file PackagePrecedesDefine.php"

#  FIXME
#  Scenario: Show an error when a DocBlock is first in a file containing an
#            package tag but directly preceeding a class definition
#    Given I am in the phpDocumentor root directory
#    When I run phpDocumentor against the file "tests/data/file-level/PackagePrecedesClass.php"
#    Then I should get a log entry "No page-level DocBlock was found in file PackagePrecedesClass.php"

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
