Feature: documentation index

  Scenario:
    Given A project named "Marios" based on "MariosPizzeria"
    And I ran "phpdoc"
    And I am on "/build/api/index.html"
    Then I should see "my doc"
