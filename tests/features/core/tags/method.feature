Feature: The @method allows a class to know which ‘magic’ methods are callable.
  To be able to define 'magic' methods on a class
  As a developer
  I want to be able add an tag to define the interface of __call

  Background:
    Given A single file named "test.php" based on "tags/MethodTag.php"
    When I run "phpdoc -f test.php"

  @wip
  Scenario: magic method with return type
    Then class "\MethodTag" has a magic method "getArray" with returntype "\A[]"

  @wip
  Scenario: magic method with return type
    Then class "\MethodTag" has a magic method "getObject" with returntype "\B"

  @wip
  Scenario: magic method with return type
    Then class "\MethodTag" has a magic method "getString" with returntype "string"

  @wip
  Scenario: magic method with return type
    Then class "\MethodTag" has a magic method "getPosition" with returntype "string"
    And class "\MethodTag" has a magic method "getPosition" with argument "integer" of type int
