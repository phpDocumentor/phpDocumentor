0002. Load extension configuration from user config during container bootstrap
==============================================================================

:Date: 2026-10-09
:Status: Accepted

Context
-------

phpDocumentor's console application builds the dependency injection container
and loads extensions before the command runs. ``Console\Application`` already
reads the ``--config`` option before the container is built and passes it to
``ContainerFactory``. ``ApplicationExtension`` then parses the user
configuration file (``phpdoc.xml`` / ``phpdoc.xml.dist``) in ``prepend()`` and
prepends the result as ``phpdocumentor`` configuration. Symfony calls
``prepend()`` on every registered extension before any ``load()`` and before
``compile()``, so the ordering needed to hand configuration to extensions
already exists.

What is missing is a way for users to configure extensions:

* the phpdoc.xml schema (``Configuration\Definition\Version3``) is strict and
  has no place for extension-specific options
* nothing routes parts of the user configuration to the extensions that were
  registered by the ``ExtensionHandler``

As a result users cannot configure extensions from ``phpdoc.xml`` in a
predictable way.

Decision
--------

Extension configuration is supported in version 3 configuration files only. It
is declared with ``<extension>`` elements, next to ``<version>``, in which the
``name`` attribute is the alias of the DI extension (``Extension::getAlias()``):

.. code-block:: xml

   <phpdocumentor configVersion="3">
       <version number="1.0.0">...</version>
       <extension name="my_extension">
           <custom-option>value</custom-option>
       </extension>
   </phpdocumentor>

The configuration is processed as follows:

1. The configuration is still parsed and validated by the classes in the
   ``phpDocumentor\Configuration`` namespace. ``Version3`` gains an
   ``extensions`` node, filled by ``<extension>`` elements and keyed by their
   ``name`` attribute. The options of an extension are accepted as-is; the
   remainder of the schema stays strict. ``Version2`` is not changed.
2. ``ApplicationExtension::prepend()`` removes the ``extensions`` node from the
   ``phpdocumentor`` configuration and, for each entry, calls
   ``prependExtensionConfig($alias, $options)``.
3. The extension receives its options in ``load()`` and is responsible for
   validating them, for example with its own ``ConfigurationInterface``.

Parsing of the user configuration stays in ``ApplicationExtension``, and
extensions are still registered before ``compile()``; routing depends on that
order.

When an ``<extension>`` refers to an alias that is not registered, the
application fails with a message naming the alias. When extensions are disabled
with ``--no-extensions``, the ``<extension>`` elements are ignored so that the
same configuration file keeps working.

Consequences
------------

Positive
~~~~~~~~

* extension packages can be configured from the user config file
* the phpdoc.xml schema stays strict: typos in core options are still reported
* config parsing stays in one place instead of being duplicated by each
  extension
* the design is compatible with Symfony's ``prepend()`` / ``load()``
  container model

Negative
~~~~~~~~

* the DI alias of an extension becomes part of its public contract; renaming it
  breaks user configuration
* extensions that do not override ``getAlias()`` get an alias derived from their
  class name, which can collide; extension authors should set it explicitly
* configuration of extensions is only available with ``configVersion="3"``
* options are not validated by phpDocumentor itself, only by the extension

Alternatives considered
-----------------------

Keep loading extensions before reading user config
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Rejected because extensions would continue to be unable to consume user-defined
configuration during compilation.

Let each extension read the config file on its own
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Rejected because it duplicates file parsing, makes configuration handling
inconsistent, and couples extensions to the application's config file layout.

Delay container creation until after the full application is started
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Rejected because the container is needed to build commands and services during
startup, and this would significantly complicate the application bootstrap
sequence.

One element per extension (``<my-extension>``) next to ``<version>``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Rejected because the root of the schema would have to accept unknown elements,
which would stop typos in core options from being reported.

Refer to extensions by their package name instead of their DI alias
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Rejected because it needs an additional lookup between the manifest and the DI
alias, while Symfony's configuration routing is keyed by alias.
