Directive list in GuidesDirectives
==================================

:Status: Done

Problem
-------

Directive reference docs are written by hand and drift from the code.
``phpdoc-guides:directive-options-list`` documents the options of one named
directive, but there was no way to generate an overview of every directive in a
project. This matters for the guides' own docs and for third-party extensions
that want to document their directives from source.

Users
-----

* **Docs authors** place ``phpdoc-guides:directive-list`` on a page and get a
  complete directive reference.
* **Directive developers** add ``#[Directive]`` and ``#[Option]`` attributes
  and a docblock, and expect them to appear in the reference.

Feature
-------

As a docs author, I want to place ``phpdoc-guides:directive-list`` on a page,
so that I get a reference of every directive in my project, generated from the
source.

Behaviour
~~~~~~~~~

* The directive takes no argument and no filters.
* It lists all non-abstract classes that carry the
  ``phpDocumentor\Guides\RestructuredText\Directives\Attributes\Directive``
  attribute, matched on the fully qualified name.
* Entries are sorted by directive name, case-insensitively. A name that is used
  by several classes is listed once, the first match wins.
* Each entry shows:

  * the name from the attribute, with an anchor id. The id is the name,
    lower-cased, with characters outside ``a-z``, ``0-9``, ``_`` and ``-``
    replaced by ``-``. Clashes with other ids on the page are up to the page
    author;
  * the aliases from the attribute;
  * the class docblock summary and description. Block tags are not shown;
  * the options from ``#[Option]``, each with name, type (``string`` when
    omitted), default and description.

* Empty parts are omitted: no aliases line, no description, no options section.
* If no directive matches, nothing is rendered.
* ``phpdoc-guides:directive-options-list`` renders its options through the same
  option markup.

Acceptance criteria
~~~~~~~~~~~~~~~~~~~

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

Out of scope
~~~~~~~~~~~~

* Filters, such as namespace or name.
* Documentation pages for the directive.
* Rendering of docblock tags.
* Output formats other than HTML.
* Restricting the list to a single API set.

Design
------

* ``DirectiveList`` is the directive. It queries the class index, filters
  abstract classes, de-duplicates and sorts in PHP.
* ``DirectiveListNode`` is a compound node whose children are
  ``DirectiveInfoNode`` instances. Each ``DirectiveInfoNode`` has
  ``DirectiveOptionNode`` children.
* ``DirectiveOptionNodeFactory`` builds the option nodes from ``#[Option]``
  attributes. ``DirectiveList`` and ``DirectiveOptionsList`` share it.
* ``DirectiveOptionsList`` returns a ``DirectiveOptionsListNode`` with
  ``DirectiveOptionNode`` children.
* Templates in ``Resources/templates/html``: ``directive-list``,
  ``directive-info``, ``directive-option`` and ``directive-options-list``.
* ``#[Directive]`` and ``#[Option]`` are matched on their fully qualified
  names, both in the query and when collecting attributes of a class.
* Attribute arguments are raw source strings in the descriptors. The factory
  and directive parse quoted strings, string lists for aliases and
  ``OptionType::Case`` values (lower-cased case name).
* The description is the class summary plus the rendered description, so inline
  tags are resolved.

Tests
-----

* Unit: ``tests/unit/Directives/DirectiveListTest.php`` and
  ``DirectiveOptionsListTest.php``, run with
  ``make unit-test-extension-guides-directives``.
* End-to-end: ``tests/e2e/cypress/integration/directive-list.spec.js`` against
  the fixture project, which has a directive with aliases and an abstract class
  carrying the attribute. The make target passes ``-it`` to Docker and needs a
  terminal.
* The scenarios are in ``directive-list.feature``. Each test names the scenario
  it covers.

Known limitations
-----------------

* ``unquote()`` strips all outer quote characters and does not unescape, so
  ``"it's"`` is mangled.
* Twig autoescape for attribute-derived values, and the ``markdown`` filter on
  descriptions, are not confirmed. The previous options template relied on the
  same behaviour.
