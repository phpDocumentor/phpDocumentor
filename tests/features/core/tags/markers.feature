Feature: Todo markers in files
  In order to add work in progress code
  As a user
  I want to be able to add markers in my code to mark todo lines

  Scenario: add TODO in code
    Given A single file named "test.php" based on "markers.php"
    When I run "phpdoc --sourcecode -f test.php"
    Then file "test.php" must contain a marker

