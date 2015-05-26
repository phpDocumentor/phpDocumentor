@installation
Feature:
  As a User
  I want to download phpDocumentor as a PHAR archive and run it
  So that I won't have to do any installation just download and run it

  Scenario: Downloading from phpdoc.org should work
    When I download "http://phpdoc.org/phpDocumentor.phar" to "phpDocumentor.phar"
     And I execute "php phpDocumentor.phar --help"
    Then the application must have run successfully

  Scenario: Downloading from Github should work
    When I download the latest phar from github to "phpDocumentor.phar"
     And I execute "php phpDocumentor.phar --help"
    Then the application must have run successfully
