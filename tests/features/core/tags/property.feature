Feature: The @property tag is used in the situation where a class contains the __get() and __set() magic methods and allows for specific names.
  To be able to document magic properties
  As a developer
  I want to be able to add a tag to define and describe a magic property

  Scenario: magic property is read correctly from class definition
    Given A single file named "test.php" based on "tags/ClassMagicProperties.php"
    When I run "phpdoc -f test.php"
    Then the application must have run successfully
    And class "\A" must have magic property "magicString" of type string

  Scenario: a magic property can be marked as read-only using a specialized tag
    Given A single file named "test.php" based on "tags/ClassMagicProperties.php"
    When I run "phpdoc -f test.php"
    Then the application must have run successfully
    And class "\A" must have magic property "readOnly" of type string

  Scenario: a magic property can be marked as write-only using a specialized tag
    Given A single file named "test.php" based on "tags/ClassMagicProperties.php"
    When I run "phpdoc -f test.php"
    Then the application must have run successfully
    And class "\A" must have magic property "writeOnly" of type string
