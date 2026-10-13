Feature: Render extension metadata in the default template
  As an extension author, I want to provide templates that render the metadata my extension attaches to descriptors,
  so that readers of the generated documentation see that metadata on the page.

  Scenario Outline: Metadata is rendered with the template of the extension that declares its key
    Given the extension "my-extension" provides a template path
    And the extension "my-extension" declares the metadata key "my_key"
    And the extension provides a template for the key "my_key" that shows the value of the metadata
    And a project with a <element> that has metadata with the key "my_key" and the value "hello"
    When the documentation is generated with the default template
    Then the page of the <element> shows "hello"

    Examples:
      | element   |
      | class     |
      | interface |
      | trait     |
      | enum      |
      | function  |
      | method    |
      | file      |

  Scenario: The template receives the metadata object and the descriptor
    Given the extension "my-extension" declares the metadata key "my_key"
    And the extension provides a template for the key "my_key" that shows the value of the metadata and the name of the descriptor
    And a project with a class "Foo" that has metadata with the key "my_key" and the value "hello"
    When the documentation is generated with the default template
    Then the page of the class "Foo" shows "hello"
    And the page of the class "Foo" shows "Foo"

  Scenario: Every metadata object of a descriptor is rendered with the template of its key
    Given the extension "my-extension" declares the metadata keys "first" and "second"
    And the extension provides a template for each of its keys that shows the value of the metadata
    And a project with a class that has metadata with the key "first" and the value "one"
    And the same class has metadata with the key "second" and the value "two"
    When the documentation is generated with the default template
    Then the page of the class shows "one"
    And the page of the class shows "two"

  Scenario: Metadata of an undeclared key is not rendered
    Given no extension declares the metadata key "unknown_key"
    And a project with a class that has metadata with the key "unknown_key" and the value "hello"
    When the documentation is generated with the default template
    Then the generation succeeds
    And the page of the class does not show "hello"
    And a warning is logged for the key "unknown_key"

  Scenario: Metadata of a declared key without a template is not rendered
    Given the extension "my-extension" declares the metadata key "my_key"
    And the extension provides no template for the key "my_key"
    And a project with a class that has metadata with the key "my_key" and the value "hello"
    When the documentation is generated with the default template
    Then the generation succeeds
    And the page of the class does not show "hello"
    And a warning is logged for the key "my_key"

  Scenario: A warning is logged for every occurrence of metadata that cannot be rendered
    Given no extension declares the metadata key "unknown_key"
    And a project with two classes that both have metadata with the key "unknown_key"
    When the documentation is generated with the default template
    Then a warning is logged for each of the two classes

  Scenario: No metadata section is shown when nothing was rendered
    Given no extension declares the metadata key "unknown_key"
    And a project with a class that has metadata with the key "unknown_key" and the value "hello"
    When the documentation is generated with the default template
    Then the page of the class shows no metadata section
