@wip
Feature:
  To be able to reference to external information
  as a developer
  I want to be able to reference to other elements in the same class

  Background:
    Given A single file named "test.php" based on "tags/internalSee.php"
    When I run "phpdoc -f test.php"

  Scenario: class level linking to external urls (http and https)
    Then class "\phpDocumentor\Descriptor\TestSeeTagIssue" has a tag see referencing url "http://www.phpdoc.org"
    And class "\phpDocumentor\Descriptor\TestSeeTagIssue" has a tag see referencing url "https://www.phpdoc.org"
    And class "\phpDocumentor\Descriptor\TestSeeTagIssue" has a tag see referencing url "http://www.phpdoc.org/docs/latest/references/phpdoc/tags/uses.html"

  Scenario: class level linking to external url (Ftp)
    Then class "\phpDocumentor\Descriptor\TestSeeTagIssue" has a tag see referencing url "ftp://somesite.nl"

  Scenario: class level self reference without description
    Then class "\phpDocumentor\Descriptor\TestSeeTagIssue" has 2 tags see referencing class descriptor "\phpDocumentor\Descriptor\TestSeeTagIssue"

  Scenario: class level self reference with description
    Then class "\phpDocumentor\Descriptor\TestSeeTagIssue" has 2 tags see referencing class descriptor "\phpDocumentor\Descriptor\TestSeeTagIssue" with description:
  """
  class itself
  """

  Scenario: class level own constant reference without description
    Then class "\phpDocumentor\Descriptor\TestSeeTagIssue" has 2 tags see referencing constant descriptor "\phpDocumentor\Descriptor\TestSeeTagIssue::CONSTANT"

  Scenario: class level own constant reference with description
    Then class "\phpDocumentor\Descriptor\TestSeeTagIssue" has 2 tags see referencing constant descriptor "\phpDocumentor\Descriptor\TestSeeTagIssue::CONSTANT" with description:
  """
  own constant
  """

  Scenario: class level own property reference without description
    Then class "\phpDocumentor\Descriptor\TestSeeTagIssue" has 2 tags see referencing property descriptor "\phpDocumentor\Descriptor\TestSeeTagIssue::$property"

  Scenario: class level own property reference with description
    Then class "\phpDocumentor\Descriptor\TestSeeTagIssue" has 2 tags see referencing property descriptor "\phpDocumentor\Descriptor\TestSeeTagIssue::$property" with description:
  """
  own property
  """

  Scenario: class level own method reference without description
    Then class "\phpDocumentor\Descriptor\TestSeeTagIssue" has 2 tags see referencing method descriptor "\phpDocumentor\Descriptor\TestSeeTagIssue::method()"

  Scenario: class level own method reference with description
    Then class "\phpDocumentor\Descriptor\TestSeeTagIssue" has 2 tags see referencing method descriptor "\phpDocumentor\Descriptor\TestSeeTagIssue::method()" with description:
  """
  own method
  """
