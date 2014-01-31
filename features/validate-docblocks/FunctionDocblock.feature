Feature: Validate whether a Function's DocBlock is valid
  In order to have correctly interpreted source code and apply clean coding
  As a documentation generating user
  I want to see if there are any errors specifically regarding my DocBlocks for global functions

#  FIXME
#  Scenario: Show an error when a DocBlock is missing for a function.
#    Given I am in the phpDocumentor root directory
#    When I run phpDocumentor against the file "tests/data/NoFunctionDocBlock.php"
#    Then I should get a log entry "No summary for function \noFunctionDocBlock()"

#  FIXME
#  Scenario: Show an error when an argument typehint mismatches with an @param tag.
#    Given I am in the phpDocumentor root directory
#    When I run phpDocumentor with:
#    """
#    <?php
#    /** @param \SimpleXMLElement $b */
#    function a(DOMElement $b) {}
#    """
#    Then I should get a log entry "The type hint of the argument is incorrect for the type definition of the @param tag with argument $b in \a()"

#  FIXME
#  Scenario: Show an error when an argument typehint mismatches with an @param tag
#            due to a missing root slash in the description.
#    Given I am in the phpDocumentor root directory
#    When I run phpDocumentor with:
#    """
#    <?php
#    namespace My\Space;
#
#    /** @param SimpleXMLElement $b */
#    function a(\SimpleXMLElement $b) {}
#    """
#    Then I should get a log entry "The type hint of the argument is incorrect for the type definition of the @param tag with argument $b in \My\Space\a()"

  Scenario: Do not show an error when an argument typehint matches an internal type.
    Given I am in the phpDocumentor root directory
    When I run phpDocumentor with:
    """
    <?php
    /** @param array $b */
    function a(array $b) {}
    """
    Then I should not get a log entry "The type hint of the argument is incorrect for the type definition of the @param tag with argument $b in \a()"

  Scenario: Do not show an error when a @param tag matches an internal type and the typehint mentions nothing.
    Given I am in the phpDocumentor root directory
    When I run phpDocumentor with:
    """
    <?php
    /** @param string $b */
    function a($b) {}
    """
    Then I should not get a log entry "The type hint of the argument is incorrect for the type definition of the @param tag with argument $b in \a()"

  Scenario: Do not show an error when an argument typehint is a FQCN but the
            Typehint must be expanded
    Given I am in the phpDocumentor root directory
    When I run phpDocumentor with:
    """
    <?php
    namespace My\Space;

    /** @param \My\Space\B $b */
    function a(B $b) {}
    """
    Then I should not get a log entry "The type hint of the argument is incorrect for the type definition of the @param tag with argument $b in \My\Space\a()"

  Scenario: Do not show an error when an argument typehint and Typehint match
            and both are FQCN
    Given I am in the phpDocumentor root directory
    When I run phpDocumentor with:
    """
    <?php
    namespace My\Space;

    /** @param \My\Space\B $b */
    function a(\My\Space\B $b) {}
    """
    Then I should not get a log entry containing "The type hint of the argument is incorrect"

  Scenario: Do not show an error when an argument typehint and Typehint match
            and both must be expanded
    Given I am in the phpDocumentor root directory
    When I run phpDocumentor with:
    """
    <?php
    namespace My\Space;

    /** @param B $b */
    function a(B $b) {}
    """
    Then I should not get a log entry containing "The type hint of the argument is incorrect"

  Scenario: Do not show an error when an argument typehint must be expanded but the
            Typehint is a FQCN
    Given I am in the phpDocumentor root directory
    When I run phpDocumentor with:
    """
    <?php
    namespace My\Space;

    /** @param B $b */
    function a(\My\Space\B $b) {}
    """
    Then I should not get a log entry containing "The type hint of the argument is incorrect"
