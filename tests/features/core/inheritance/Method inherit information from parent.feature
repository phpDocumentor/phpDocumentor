Feature: Classes inherit information from parent classes

  Background:
    Given A single file named "test.php" based on "multiParentInheritance.php"
    When I run "phpdoc -f test.php"

  Scenario: Inherit summary from a parent interface
    Then class "\Example" has method doSomething with summary:
      """
      Do something with $object and return that it worked
      """
    And class "\DeepExample" has method doSomething with summary:
      """
      Do something with $object and return that it worked
      """
