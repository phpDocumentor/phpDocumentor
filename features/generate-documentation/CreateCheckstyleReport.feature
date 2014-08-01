Feature: Create a report of all errors formatted using the CheckStyle format
  In order to track and report errors using a Continuous Integration Environment
  As a user
  I want all errors to be recorded in an XML file using the CheckStyle format

  Background:
    Given I am in the phpDocumentor root directory

  Scenario: Write errors to an XML file.
    Given a source file containing validation errors
    When I run phpDocumentor with the "checkstyle" template
    Then I should get a file "checkstyle.xml" containing checkstyle error definitions

  Scenario: Write warnings to an XML file.
    Given a source file containing validation warnings
    When I run phpDocumentor with the "checkstyle" template
    Then I should get a file "checkstyle.xml" containing checkstyle warning definitions

  Scenario: Receive an empty definition when there are no errors.
    Given a source file containing no errors
    When I run phpDocumentor with the "checkstyle" template
    Then I should get a file "checkstyle.xml" containing no definitions

  Scenario: Write errors to an XML file by adding the Checkstyle writer to the config.
    Given a source file containing validation errors
      And the configuration file has a transformation with the "checkstyle" writer having "checkstyle.xml" as artifact
    When I run phpDocumentor with the "responsive-twig" template
    Then I should get a file "checkstyle.xml" containing checkstyle error definitions

