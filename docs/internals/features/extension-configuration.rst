Extension configuration in phpdoc.xml
=====================================

:Related ADR: ``adr/0002-user-config-before-extension-loading.rst``
:Status: In progress

Requirements
------------

Problem
~~~~~~~

Users cannot configure extensions from ``phpdoc.xml``. The version 3 schema is
strict and has no place for extension options, and nothing routes parts of the
user configuration to the extensions registered by the ``ExtensionHandler``.

Users
~~~~~

* **Extension authors** decide which settings their extension supports and
  document them.
* **Project owners** set those settings in the ``phpdoc.xml`` of their project
  when they use phpDocumentor with an extension.

Behaviour
~~~~~~~~~

An ``<extension name="<di-alias>">`` block in a ``configVersion="3"``
configuration file is delivered to the extension with that DI alias as
configuration in its ``load()`` method.

.. code-block:: xml

   <phpdocumentor configVersion="3">
       <version number="1.0.0">...</version>
       <extension name="my_extension">
           <custom-option>value</custom-option>
       </extension>
   </phpdocumentor>

Constraints
~~~~~~~~~~~

* Parsing and validation of ``phpdoc.xml`` stay in the
  ``phpDocumentor\Configuration`` namespace and ``ApplicationExtension``; no
  second parser is introduced.
* The rest of the schema stays strict.
* Extensions validate their own options; the core only routes them.
* Routing must happen before the container is compiled.
* Only version 3 configuration files support extension configuration.

Out of scope
~~~~~~~~~~~~

* extension configuration in the version 2 upgrade path
* configuration of extensions through command line options
* referring to extensions by their manifest (package) name
* a dedicated error for an ``<extension>`` block of an alias that is not registered while extensions are enabled
* a dedicated error for ``<extension>`` blocks in version 2 configuration files

Feature
-------

As a project owner, I want to set options for an extension in ``phpdoc.xml``, so
that the extension behaves as I configured it, and I want that same file to keep
working when extensions are disabled.

Scope
   A version 3 configuration with an ``<extension name>`` block that is routed
   to the extension with that DI alias, observable in the CLI output. When
   extensions are disabled with ``--no-extensions``, the block is ignored.

Out of scope
   Unknown alias error, version 2 behaviour, CLI flags and manifest-name lookup.

Acceptance criteria
   * A version 3 ``phpdoc.xml`` with an ``<extension name="...">`` block is
     accepted without a schema error.
   * The extension with that alias receives the configured options in
     ``load()``.
   * The CLI output of a fixture extension shows a value derived from the
     configured option.
   * Running with ``--no-extensions`` on the same configuration succeeds, lists
     no loaded extensions and shows no option of the extension.

Decisions
---------

* Options are keyed by the ``name`` attribute of ``<extension>``, and the name is removed from the options that the
  extension receives.
* The ``extensions`` node is part of the default output of ``Version3`` (as an empty array).
* The XSD ``data/xsd/phpdoc.xsd`` accepts ``<extension name="...">`` with free-form content, after ``<template>``.
* The fixture extension of the CLI test writes the option to ``STDOUT`` in ``load()``, because extensions have no
  other simple hook to produce output.
* The stray ``<extension-nama>`` sketch in the repository's ``phpdoc.dist.xml`` was removed.
* Ignoring ``<extension>`` blocks with ``--no-extensions`` needs no explicit code: Symfony accepts prepended configuration
  for an alias that is not registered, so the blocks are ignored as long as no extension with that alias is loaded. The
  CLI test documents this behaviour.
