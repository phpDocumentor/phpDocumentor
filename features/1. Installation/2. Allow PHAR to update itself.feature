@installation
Feature:
  As a User
  I want to update the PHAR archive to the latest version using a Single Command
  So that users have a download option where they do not have to install the app but just download it

  Scenario: Updating to the latest version works
    When I download the latest phar from github to "phpDocumentor.phar"
     And I execute "php phpDocumentor.phar selfupdate"
    Then the application must have run successfully

  Scenario: Installation types other than PHAR do not have the selfupdate function
    Given I am in the phpDocumentor root directory
      And I execute "php bin/phpdoc selfupdate"
      And the application returns an error containing "Command "selfupdate" is not defined"
