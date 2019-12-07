@wip
Feature:
  To be able to reference to external information
  as a developer
  I want to be able to reference to other elements in the same class

  Background:
    Given A single file named "test.php" based on "tags/internalUses.php"
    When I run "phpdoc -f test.php"

  Scenario: class level self reference without description
    Then class "\phpDocumentor\Descriptor\TestUsesTagIssue" has 2 tags uses referencing class descriptor "\phpDocumentor\Descriptor\TestUsesTagIssue"

  Scenario: class level self reference with description
    Then class "\phpDocumentor\Descriptor\TestUsesTagIssue" has 2 tags uses referencing class descriptor "\phpDocumentor\Descriptor\TestUsesTagIssue" with description:
  """
  class itself
  """

  Scenario: class level own constant reference without description
    Then class "\phpDocumentor\Descriptor\TestUsesTagIssue" has 2 tags uses referencing constant descriptor "\phpDocumentor\Descriptor\TestUsesTagIssue::CONSTANT"

  Scenario: class level own constant reference with description
    Then class "\phpDocumentor\Descriptor\TestUsesTagIssue" has 2 tags uses referencing constant descriptor "\phpDocumentor\Descriptor\TestUsesTagIssue::CONSTANT" with description:
  """
  own constant
  """

  Scenario: class level own property reference without description
    Then class "\phpDocumentor\Descriptor\TestUsesTagIssue" has 2 tags uses referencing property descriptor "\phpDocumentor\Descriptor\TestUsesTagIssue::$property"

  Scenario: class level own property reference with description
    Then class "\phpDocumentor\Descriptor\TestUsesTagIssue" has 2 tags uses referencing property descriptor "\phpDocumentor\Descriptor\TestUsesTagIssue::$property" with description:
  """
  own property
  """

  Scenario: class level own method reference without description
    Then class "\phpDocumentor\Descriptor\TestUsesTagIssue" has 2 tags uses referencing method descriptor "\phpDocumentor\Descriptor\TestUsesTagIssue::method()"

  Scenario: class level own method reference with description
    Then class "\phpDocumentor\Descriptor\TestUsesTagIssue" has 2 tags uses referencing method descriptor "\phpDocumentor\Descriptor\TestUsesTagIssue::method()" with description:
  """
  own method
  """
