@installation
Feature:
  As a User
  I want to install phpDocumentor using Composer
  So that I can include it in my project

  Scenario: Install phpDocumentor with Composer
    When I execute "composer require phpdocumentor/phpdocumentor --no-interaction"
    Then the exit code should be zero
     And the output should contain:
      """
      Generating autoload files
      """