Feature: Api tag can be used to mark Structural elements as being suitable for consumption by third parties.
  To be sure that my users are using the right Structural elements
  As a developer
  I want to be able to mark Structural elements as being suitable for consumption by third parties

  Background:
    Given A single file named "test.php" based on "tags/Api.php"
    When I run "phpdoc -f test.php"

  Scenario: Api tag is added to class
    Then class "\A" has exactly 1 tag api

  Scenario: Api tag is added to methods
    Then class "\A" has a method named someMethod with exactly 1 tag api
    And class "\A" has a method named someOtherMethod without tag api

  Scenario: Api tag is not inherited
    Then class "\B" without tag api
