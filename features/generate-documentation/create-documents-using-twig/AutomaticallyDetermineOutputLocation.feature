Feature: Determine output location per element type
  In order to have a predictable path to an elements' documentation
  As a User
  I want phpDocumentor to automatically determine a correct output location per element

  Background:
    Given I am in the phpDocumentor root directory

  @router
  Scenario: Write file to `include/[fileName].html`

  Scenario: Write namespace to `[namespace]/[subNamespace]/namespace.html`

  Scenario: Write class to `[namespace]/[subNamespace]/className.html`

  Scenario: Write interface to `[namespace]/[subNamespace]/interfaceName.html`

  Scenario: Write trait to `[namespace]/[subNamespace]/traitName.html`
