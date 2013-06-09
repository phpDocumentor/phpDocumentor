Feature: Validate if a structural element has a Short Description in a DocBlock
  In order to validate the correctness of my DocBlocks
  As a quality assurance maintainer
  I want to see if every existing DocBlock has a short description

  Scenario: Log an error when a File DocBlock is missing a Short Description.
    Given I am in the phpDocumentor root directory
    When I run phpDocumentor with:
    """
    <?php
    /** */

    namespace Foo;
    """
    Then I should get a log entry "No short description for NoShortDescription.php"

  Scenario: Log an error when a Function DocBlock is missing a Short Description.
    Given I am in the phpDocumentor root directory
    When I run phpDocumentor with:
    """
    <?php
    /** */
    function noShortDescriptionFunction() { }
    """
    Then I should get a log entry "No short description for \noShortDescriptionFunction()"

  Scenario: Log an error when a Constant DocBlock is missing a Short Description.
    Given I am in the phpDocumentor root directory
    When I run phpDocumentor with:
    """
    <?php
    /** */
    const NO_SHORT_DESCRIPTION = 'a';
    """
    Then I should get a log entry "No short description for \NO_SHORT_DESCRIPTION"

  Scenario: Log an error when a Class DocBlock is missing a Short Description.
    Given I am in the phpDocumentor root directory
    When I run phpDocumentor with:
    """
    <?php
    /** */
    class NoShortDescription
    {
    }
    """
    Then I should get a log entry "No short description for \NoShortDescription"

  Scenario: Log an error when a Property DocBlock is missing a Short Description.
    Given I am in the phpDocumentor root directory
    When I run phpDocumentor with:
    """
    <?php
    class NoShortDescription
    {
        /** */
        protected $noShortDescriptionProperty;
    }
    """
    Then I should get a log entry "No short description for $noShortDescriptionProperty"

  Scenario: Log an error when a Method DocBlock is missing a Short Description.
    Given I am in the phpDocumentor root directory
    When I run phpDocumentor with:
    """
    <?php
    class NoShortDescription
    {
        /** */
        public function noShortDescriptionFound() { }
    }
    """
    Then I should get a log entry "No short description for noShortDescriptionFound()"
