Feature:
  As a User
  I want to use an interactive wizard on the command line to create a configuration file
  So that it is easy to initialize a configuration file

  @roadmap-v3
  Scenario: Create a configuration file using the `init` command
    Given I am in the phpDocumentor root directory
     When I run "phpdoc init"
     Then the application must have run successfully
      And file "phpdoc.dist.xml" should exist