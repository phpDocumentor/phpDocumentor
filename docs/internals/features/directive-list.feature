Feature: Directive list in GuidesDirectives
  As a docs author, I want to place the directive list on a page,
  so that I get a reference of every directive in my project.

  Scenario: Every non-abstract directive class is listed in alphabetical order with an anchor
    Given a project with the directives "zeta" and "alpha"
    When a page uses the directive list
    Then the page shows an entry for "alpha"
    And the page shows an entry for "zeta"
    And the entry "alpha" is shown before the entry "zeta"
    And each entry can be linked to by an anchor derived from its name

  Scenario: Aliases are shown
    Given a project with the directive "example-directive" that has the aliases "example" and "sample"
    When a page uses the directive list
    Then the entry "example-directive" shows the aliases "example" and "sample"

  Scenario: The aliases line is omitted without aliases
    Given a project with the directive "example-directive" that has no aliases
    When a page uses the directive list
    Then the entry "example-directive" shows no aliases

  Scenario: The class docblock is shown as the description
    Given a project with the directive "example-directive" whose class has a docblock with a summary and a description
    When a page uses the directive list
    Then the entry "example-directive" shows the summary and the description

  Scenario: Docblock tags are not shown
    Given a project with the directive "example-directive" whose docblock contains a "@deprecated" tag
    When a page uses the directive list
    Then the entry "example-directive" does not show the tag

  Scenario: Options are listed with their details
    Given a project with the directive "example-directive" that has the option "template" of type string
    And the option "force" of type boolean with the default "false"
    When a page uses the directive list
    Then the entry "example-directive" lists the option "template" with the type "string"
    And the entry lists the option "force" with the type "boolean" and the default "false"

  Scenario: An option without a type is shown as a string
    Given a project with the directive "example-directive" that has an option without a type
    When a page uses the directive list
    Then the option is shown with the type "string"

  Scenario: The options section is omitted without options
    Given a project with the directive "example-directive" that has no options
    When a page uses the directive list
    Then the entry "example-directive" shows no options section

  Scenario: Abstract directive classes are not listed
    Given a project with the directive "example-directive"
    And an abstract class carrying the directive attribute named "abstract-directive"
    When a page uses the directive list
    Then the page shows no entry for "abstract-directive"

  Scenario: Nothing is rendered without directives
    Given a project without any directive classes
    When a page uses the directive list
    Then the page shows no directive list
