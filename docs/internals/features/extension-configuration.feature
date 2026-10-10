Feature: Extension configuration in phpdoc.xml
  As a project owner, I want to set options for an extension in phpdoc.xml,
  so that the extension behaves as I configured it.

  Scenario: An extension receives the options configured in phpdoc.xml
    Given a project with a version 3 configuration file
    And the extension "my_extension" is installed in the project
    And the configuration contains an extension block for "my_extension" with the option "custom-option" set to "hello"
    When phpDocumentor runs
    Then the run succeeds
    And the extension "my_extension" reports the option "custom-option" with the value "hello"

  Scenario: An extension block is accepted by the configuration schema
    Given a project with a version 3 configuration file
    And the configuration contains an extension block for "my_extension" with the option "custom-option" set to "hello"
    When the configuration is loaded
    Then no configuration error is reported
    And the other configuration settings keep their values
