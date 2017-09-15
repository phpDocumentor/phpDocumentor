Feature: Parsing file with a class
  In order to create api documentation for a class
  As a developer
  I need to be able to define a docblock for a class so I can describe what's the purpose of the class

  Scenario: Class is present in the project
    Given A single file named "test.php" based on "NoPackagePrecedesClass.php"
    When I run "phpdoc -f test.php"
    Then the application must have run successfully
    And the AST has a class named "Foo" in file "test.php"

#  Scenario: Class is located in the default package
#    Given A single file named "test.php" based on "NoPackagePrecedesClass.php"
#    When I run "phpdoc -f test.php"
#    Then the application must have run successfully
#    And the class named "Foo" is in the default package

  Scenario: When a file contains one docblock before a class this docblock is associated with the class.
    Given A single file named "test.php" based on "NoPackagePrecedesClass.php"
    When I run "phpdoc -f test.php"
    Then the application must have run successfully
    And the class named "Foo" has docblock with content:
    """
    This DocBlock will attach to the class statement
    """
