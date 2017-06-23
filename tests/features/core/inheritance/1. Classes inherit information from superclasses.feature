Feature: Classes inherit information from parent classes

  Background:
    Given A single file named "test.php" based on "basicClassInheritance.php"
    When I run "phpdoc -f test.php"

  Scenario: Inherit summary from a parent class
    Then class "\B" has summary:
      """
      This is a class summary
      """
    And class "\C" has summary:
      """
      This is a class summary
      """

  Scenario: Inherit description from a parent class
    Then class "\B" has description:
      """
      This is a class description.
      """
    And class "\C" has description:
      """
      This is a class description.
      """

  Scenario: Current description is augmented with that of parent class when using {@inheritdoc}
  Scenario: Inherit constants from a parent class
  Scenario: Inherit methods from a parent class
  Scenario: Inherit properties from a parent class
  Scenario: Inherit @method tags from a parent class
  Scenario: Inherit @property tags from a parent class
  Scenario: Inherit @property-read tags from a parent class
  Scenario: Inherit @property-write tags from a parent class
  Scenario: Inherit @package tags from a parent class
  Scenario: Inherit @subpackage tags from a parent class if @package is equal
  Scenario: Inherit @author tags from a parent class
  Scenario: Inherit @copyright tags from a parent class
  Scenario: Inherit @version tags from a parent class
    Then class "\B" has version 1.0
    And class "\C" has version 1.0

