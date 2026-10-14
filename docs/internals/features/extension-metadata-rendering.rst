Extension metadata rendering in the default template
====================================================

:Status: Requirements draft

Requirements
------------

Problem
~~~~~~~

Descriptors implementing ``MetadataAwareInterface`` (classes, interfaces,
traits, enums, functions and methods) carry ``Metadata`` items, but the default
template never renders them and extensions cannot contribute templates. We want
an immediate use case (our own extension) and a general extension point for
third-party authors.

Users
~~~~~

* **Extension authors** produce ``Metadata`` and ship templates that render it.
* **Docs site owners** install extensions and may override extension templates.
* **Docs readers** see the metadata on the rendered page.

Behaviour
~~~~~~~~~

* Extensions get a Twig namespace equal to their alias (``@<alias>/...``).
  Aliases are restricted to alphanumeric characters and ``-``, so they are
  always valid Twig namespaces. Validating this is not part of this feature.
* Extensions provide the path to their templates. That path is registered under
  the extension's alias namespace when the Twig environment is created.
* An extension declares the metadata keys it handles as a list of keys. The
  template is derived by convention:
  ``@<alias>/metadata/<key>.html.twig``.
* The default template renders metadata for the metadata-aware types: classes,
  interfaces, traits, enums, functions, methods and files. For
  each ``Metadata`` object on a descriptor, the template for its key is
  rendered, receiving ``metadata`` (the ``Metadata`` implementation created by
  the extension) and ``node`` (the descriptor).
* The default template provides only a minimal wrapper; extension templates
  decide their own markup. The wrapper is omitted when nothing renders.
* Site owners can override an extension's template through the existing
  template-overrides directory.
* Keys with no declaring extension, or declared keys whose template is missing,
  are skipped and a warning is logged for every occurrence.
* The metadata format is not predefined. A ``Metadata`` implementation is
  defined by the extension and only guarantees ``key()``. The template an
  extension ships is therefore coupled to that extension's own metadata class
  and uses its getters.

Constraints
~~~~~~~~~~~

* Respect ``BC_POLICY.md`` (no breaking changes to public API).
* Output is identical when no metadata or extensions are present.

Out of scope
~~~~~~~~~~~~

* Non-HTML writers.
* Changes to the Reflection package (``Metadata::key()`` is unchanged).
* Custom (non-default) templates; only the default template renders metadata.
* Two extensions declaring the same key (collision handling).
* Producing metadata. Extensions are responsible for producing the ``Metadata``
  objects on the reflection elements; this feature only renders them.
* Metadata on namespaces. A reflected namespace has no extension hook without
  changes to the Reflection package, so namespace pages do not render metadata
  in this slice.
* Metadata on packages, and index, search or graph output.

Open points
~~~~~~~~~~~

* The extension base class is a Symfony DI extension (``getAlias()``,
  ``load()``), not a Twig extension. The template path and metadata keys need
  to be readable from it when the Twig environment is created.

Feature description
-------------------

Feature: Render extension metadata in the default template
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

As an extension author, I want to provide templates that render the metadata my
extension attaches to descriptors, so that readers of the generated
documentation see that metadata on the page.

Slice: the metadata-aware descriptor types classes, interfaces, traits, enums,
functions, methods and files in the default template. Chosen over
a methods-only slice because the shared pipeline is the bulk of the work and
each additional type is a one-line include plus one scenario.

Scope
   Extension template path registered under the extension's alias; key
   declaration; template lookup by key; rendering for all seven descriptor
   types; skipping with a warning; unchanged output when unused.

Out of scope for this slice
   Site owners overriding extension templates through the overrides directory
   (deferred to a follow-up slice). Everything listed under *Out of scope*
   above.

Acceptance criteria
   * [x] An extension that provides a template path and declares a key gets its
     template rendered for each ``Metadata`` object with that key, on a class,
     interface, trait, enum, function, method and file page.
   * [x] The template receives the extension's ``Metadata`` object as
     ``metadata`` and the descriptor as ``node``.
   * [x] Metadata for a key that no extension declares is not rendered and a
     warning is logged for each occurrence.
   * [x] Metadata for a declared key whose template does not exist is not
     rendered and a warning is logged for each occurrence.
   * [x] No metadata wrapper is output when nothing was rendered.
   * [x] Generated output is identical to before when there is no metadata and
     no extension provides templates.

Decisions
---------

* Extensions expose their template directory and metadata keys through two
  optional methods on ``phpDocumentor\Extension\Extension``:
  ``getTemplatePath()`` (default ``null``) and ``getMetadataKeys()`` (default
  ``[]``). Both are non-abstract, so existing extensions keep working.
* ``ContainerFactory`` collects them, indexed by the extension alias, in the
  container parameter ``phpdoc.extension.metadata_templates``.
  ``EnvironmentFactory`` registers each path as a Twig namespace named after
  the alias. The new constructor arguments are optional.
* ``MetadataTemplateResolver`` maps a metadata key to
  ``@<alias>/metadata/<key>.html.twig`` and logs a warning for every metadata
  object that cannot be rendered. It is exposed to templates as the
  ``metadata_template()`` function. The ``metadataAware`` test guards the
  component.
* The default template renders metadata with
  ``components/metadata.html.twig``, included from ``element-header`` (classes,
  interfaces, traits), the enum, file, method and function templates.
* Core change: ``AssemblerFactory`` now gives the ``MetadataReducer`` to the
  class, trait, enum, interface, method and function assemblers. Before this
  only the file and property hook assemblers did, so metadata never reached
  these descriptors.
* Producing metadata stays with the extension. The reflection package applies
  reducers to elements created by ``AbstractFactory`` but not to files; the
  test extension attaches file metadata with a parser middleware instead.
* Tests: Cypress in ``extensions/MetadataExample/tests/e2e`` (scenarios 1 to
  5), PHPUnit in ``tests/unit/.../Writer/Twig`` (scenarios 4 to 7 and the alias
  namespace). Run the end-to-end tests with ``make e2e-test-metadata-example``.
* An extension whose template path is not a directory is skipped and a warning
  is logged, so one misconfigured extension cannot break the Twig writer.
* Known: when two extensions declare the same key, the first one registered
  wins. Collision handling is out of scope for this slice.
* Verification note: ``make functional-test`` reports failures that were
  already present before this feature and were not investigated here. The unit
  (879), integration (54) and Cypress (11) suites pass.
