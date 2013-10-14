Feature: Specify the location for logs in a custom text format
  In order to provide an artifact detailing the execution details
  As a project maintainer
  I want to generate one or more logs with details of the execution

  Background:
    Given I am in the phpDocumentor root directory
    And I have removed all files with the "log" extension

  Scenario: Do not generate a log file by default
    When I run phpDocumentor with:
    """
    <?php
    namespace My\Name\Space {}
    """
    Then the exit code should be zero
    And there should be no files with the "log" extension

  Scenario: Generate a log file at the location indicated by the `--log` option of a command

  Scenario: Generate a log file at the location indicated by the `logging->paths->default` path in the configuration

  Scenario: By default INFO, NOTICE and DEBUG messages should not be logged

  Scenario: When the -v option is provided should INFO messages also be logged

  Scenario: When the -vv option is provided should INFO and NOTICE messages also be logged

  Scenario: When the -vvv option is provided should INFO, NOTICE and DEBUG messages also be logged
