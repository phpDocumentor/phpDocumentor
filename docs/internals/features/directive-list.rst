Directive list in GuidesDirectives
==================================

:Status: Verified

Requirements
------------

Problem
~~~~~~~

Directive reference docs are written by hand and drift from the code.
``phpdoc-guides:directive-options-list`` documents the options of one named
directive, but there is no way to generate an overview of every directive in a
project. This matters for the guides' own docs and for third-party extensions
that want to document their directives from source.

Users
~~~~~

* **Docs authors** place ``phpdoc-guides:directive-list`` on a page and get a
  complete directive reference.
* **Directive developers** add ``#[Directive]`` and ``#[Option]`` attributes
  and a docblock, and expect them to appear in the reference.

Behaviour
~~~~~~~~~

* RST directive: ``phpdoc-guides:directive-list``. It takes no argument and no
  filters.
* It selects all non-abstract classes carrying the
  ``phpDocumentor\Guides\RestructuredText\Directives\Attributes\Directive``
  attribute. The match is on the fully qualified name, which phpDocumentor
  resolves.
* Scope is a single API set. Several API sets and duplicate directive names are
  not handled in this slice.
* Entries are sorted alphabetically by directive name.
* Each entry shows:

  * the name, taken from the attribute, with a stable anchor id derived from
    the name;
  * the aliases, taken from the attribute;
  * the full class docblock (summary and description), with tags ignored;
  * the options, taken from ``#[Option]``, each with name, type (default
    ``string``), default and description.

* Empty parts are omitted: no aliases line, no description, or no options
  section.
* If no directive matches, nothing is rendered.

Structure
~~~~~~~~~

* ``DirectiveListNode`` is a compound node whose children are
  ``DirectiveInfoNode`` instances.
* ``DirectiveInfoNode`` has ``DirectiveOptionNode`` children.
* Follow-up slice: the existing ``DirectiveOptionsList`` node and directive are
  refactored to build ``DirectiveOptionNode`` children and share the option
  template. The node is renamed ``DirectiveOptionsListNode``.

Constraints
~~~~~~~~~~~

* HTML output only, through twig templates, as the existing directive does.
* Follow the existing extension structure: ``Directives/``, ``Nodes/``,
  ``Resources/config/services.php`` and ``Resources/templates/html``.

Testing
~~~~~~~

* Unit tests: abstract classes are skipped, results are sorted by name, the
  result is ``null`` when nothing matches and aliases and options are mapped
  onto the child nodes.
* E2E: the fixture project gets a second directive with aliases and an abstract
  class carrying the attribute, and a Cypress spec checks the rendered list.

Out of scope
~~~~~~~~~~~~

* Filters, such as namespace or name.
* Documentation pages for the directive.
* Rendering of docblock tags.
* Other output formats.
* Multiple API sets and duplicate directive names.
* Refactoring ``DirectiveOptionsList`` (follow-up slice).

Open assumptions
~~~~~~~~~~~~~~~~

* "Focus on 1 API set" is handled by listing each directive name once, the
  first match wins, instead of restricting the query to a single API set.

Feature description
-------------------

As a docs author, I want to place ``phpdoc-guides:directive-list`` on a page,
so that I get a reference of every directive in my project, generated from the
source.

Scope: the new directive, ``DirectiveListNode``, ``DirectiveInfoNode`` and
``DirectiveOptionNode``, and their twig templates. The directive lists
non-abstract classes with the ``Directive`` attribute, sorted by name. Each
entry has an anchor id, aliases, the full docblock and its options.

Out of scope: refactoring ``DirectiveOptionsList`` onto the shared option node
and template (a follow-up slice), filters, docs pages, docblock tags, other
output formats and multiple API sets.

Acceptance criteria:

* [x] A page with ``phpdoc-guides:directive-list`` renders one entry per
  non-abstract class carrying the ``Directive`` attribute.
* [x] Entries are ordered alphabetically by directive name.
* [x] Each entry has a stable anchor id derived from the directive name.
* [x] An entry shows the aliases from the attribute, and omits the aliases line
  when there are none.
* [x] An entry shows the full class docblock, and omits it when empty.
* [x] An entry lists its ``#[Option]`` attributes with name, type (default
  ``string``), default and description, and omits the section when there are
  none.
* [x] Abstract classes with the attribute are not listed.
* [x] When no directive matches, nothing is rendered.

Decisions
---------

* Attributes are matched on their fully qualified name, both in the query and
  when collecting the ``Directive`` and ``Option`` attributes of a class.
* Attribute arguments are raw source strings in the descriptors. The directive
  parses quoted strings, string lists for aliases, and ``OptionType::Case``
  values (lower-cased case name) itself.
* The description is the class summary plus the rendered description. Inline
  tags are resolved by the description descriptor, block tags are ignored.
* Abstract filtering, de-duplication by name and sorting happen in PHP.
* Anchor ids are ``directive-<name>``.

Verification
------------

* Unit tests (``make unit-test-extension-guides-directives``): 16 passing.
* End-to-end (Cypress against the fixture project): 11 passing, 8 of them
  new. The make target passes ``-it`` to Docker and needs a terminal.
* ``phpcs`` is clean for the extension's sources and unit tests.
* A read-only review found three issues, all fixed: docblock placeholders, attribute matching on
  the short name, and duplicate directive names.

Follow-up: DirectiveOptionsList refactor
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Done. ``DirectiveOptionsList`` now builds ``DirectiveOptionNode`` children
through the shared ``DirectiveOptionNodeFactory``, its node is renamed
``DirectiveOptionsListNode``, and ``directive-list-options.html.twig`` is
replaced by ``directive-options-list.html.twig``, which renders the options
through the shared ``directive-option`` template. The existing unit and e2e
tests keep passing.

Known follow-ups
~~~~~~~~~~~~~~~~

* Quote handling in ``unquote()`` strips all outer quote characters and does
  not unescape.
* Sorting is case-sensitive, and anchors are not slugified.
* Confirm twig autoescape for attribute-derived values, and the ``markdown``
  filter on descriptions, as in the existing options template.
