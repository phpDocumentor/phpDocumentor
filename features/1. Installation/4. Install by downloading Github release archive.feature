@installation
Feature:
  As a User
  I want to install phpDocumentor by downloading the distribution from Github
  So that I can install phpDocumentor by on an offline machine by downloading it and setting it up

  Scenario: Download and run archive
    When I download and unpack the latest phpDocumentor.tgz
     And I execute "php bin/phpdoc --help"
    Then the application must have run successfully
