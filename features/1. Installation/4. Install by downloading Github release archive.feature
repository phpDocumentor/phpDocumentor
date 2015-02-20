@installation
Feature:
  As a User
  I want to install phpDocumentor by downloading the distribution from Github
  So that I can install phpDocumentor by on an offline machine by downloading it and setting it up

  Scenario: Download and run archive
    When I download and unpack "https://github.com/phpDocumentor/phpDocumentor2/releases/download/v2.8.1/phpDocumentor-2.8.1.tgz"
     And I execute "php bin/phpdoc --help"
    Then the application must have run successfully
