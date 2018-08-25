Feature: The @return tag is used to document the return value of functions or methods.
  To be able to document my method and functions return types
  As a developer
  I want to be able add an return tag to methods and functions

  Background:
    Given A single file named "test.php" based on "tags/return.php"
    When I run "phpdoc -f test.php"

  Scenario: method without docblock without return
    Then class "\ReturnTag" has a method "getReturnWithoutAny"
    And class "\ReturnTag" has a method "getReturnWithoutAny" without returntype

  Scenario: method without docblock with return type
    Then class "\ReturnTag" has a method "get"
    And class "\ReturnTag" has a method "get" with returntype 'int' without description

  Scenario: method with docblock without return type
    Then class "\ReturnTag" has a method "getReturnDescription"
    And class "\ReturnTag" has a method "getReturnDescription" with returntype 'string' with description:
    """
    some value
    """

  Scenario: method without docblock with return type
    Then class "\ReturnTag" has a method "getMultiTypeArray"
    And class "\ReturnTag" has a method "getMultiTypeArray" with returntype '(int|string)[]' without description

  Scenario: method with docblock without return type
    Then class "\ReturnTag" has a method "getReturnWithDefinedReturn"
    And class "\ReturnTag" has a method "getReturnWithDefinedReturn" with returntype 'string' with description:
    """
    description
    """

  Scenario: function without docblock without return
    Then has function "getReturnWithoutAny" without returntype

  Scenario: function without docblock with return type
    Then has function "get" with returntype 'int' without description

  Scenario: function with docblock without return type
    Then has function "getReturnDescription" with returntype 'string' with description:
    """
    some value
    """

  Scenario: function with docblock without return type
    Then has function "getReturnWithDefinedReturn" with returntype 'string' with description:
    """
    description
    """
