Feature: The @param tag is used to document a single argument of a function or method.
  To be able to document an argument of a function or method
  As a developer
  I want to be able add an tag to define the type and a description

  Scenario:
    Given A single file named "test.php" based on "tags/param.php"
    When I run "phpdoc -f test.php"
    Then the application must have run successfully
    And argument param of function "\test" has no defined type and description is:
    """
    some description
    """
