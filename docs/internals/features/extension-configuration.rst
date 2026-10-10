Extension configuration in phpdoc.xml
=====================================

:Related ADR: ``adr/0002-user-config-before-extension-loading.rst``
:Status: Implemented (first slice)

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

The following behaviours are part of ADR 0002 but are deferred to follow-up
slices: failing on an unregistered alias, ignoring ``<extension>`` blocks when
``--no-extensions`` is used, and rejecting ``<extension>`` in version 2 files.

Feature
-------

As a project owner, I want to set options for an extension in ``phpdoc.xml``, so
that the extension behaves as I configured it.

Scope
   A version 3 configuration with an ``<extension name>`` block that is routed
   to the extension with that DI alias, observable in the CLI output.

Out of scope
   Unknown alias error, ``--no-extensions`` handling, version 2 behaviour, CLI
   flags and manifest-name lookup.

Acceptance criteria
   * A version 3 ``phpdoc.xml`` with an ``<extension name="...">`` block is
     accepted without a schema error.
   * The extension with that alias receives the configured options in
     ``load()``.
   * The CLI output of a fixture extension shows a value derived from the
     configured option.

Decisions
---------

* Options are keyed by the ``name`` attribute of ``<extension>``, and the name is removed from the options that the
  extension receives.
* The ``extensions`` node is part of the default output of ``Version3`` (as an empty array).
* The XSD ``data/xsd/phpdoc.xsd`` accepts ``<extension name="...">`` with free-form content, after ``<template>``.
* The fixture extension of the CLI test writes the option to ``STDOUT`` in ``load()``, because extensions have no
  other simple hook to produce output.
* The stray ``<extension-nama>`` sketch in the repository's ``phpdoc.dist.xml`` was removed.
