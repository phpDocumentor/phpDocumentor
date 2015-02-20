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
    When I download "http://github.com/phpDocumentor/phpDocumentor2/releases/download/v2.8.1/phpDocumentor.phar" to "phpDocumentor.phar"
     And I execute "php phpDocumentor.phar --help"
    Then the application must have run successfully
