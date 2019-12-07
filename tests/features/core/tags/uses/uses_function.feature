@wip
Feature:
  To be able to reference to external information
  as a developer
  I want to be able to reference to functions inside the same namespace

  Scenario:
    Given A single file named "test.php" based on "tags/see_function.php"
    When I run "phpdoc -f test.php"
    Then the ast has a function named "\SmartFactory\echo_html"
    And the ast has a function named "\SmartFactory\escape_html"
    And function "SmartFactory\echo_html" has tag uses referencing "\SmartFactory\escape_html()"
