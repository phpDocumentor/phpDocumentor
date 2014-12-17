Reference Documentation Generation
==================================

Introduction
------------

phpDocumentor's mission is to provide developers and technical writers with the means to manage the documentation for
their project. Part of this mission is the ability to generate true reference documentation.

Features
--------

- Can read Markdown
- Can read reST
- Can read DocBook
- Can output HTML (wrapped in a template)
- Supports versions
- Supports internationalization

Workings
--------

As with the interpretation of PHP source code files, phpDocumentor should process reference documentation files
in two phases: parsing and transformation.

The parsing phase normalizes the given input formats into DocBook and the transformation phase transforms the DocBook
representation into a series of files with the expected output format using a dedicated writer.

.. info:: In the first version we only support HTML as output format.

Parsing phase
~~~~~~~~~~~~~

phpDocumentor should interpret the given series of Markdown, reST and DocBook files, normalize these to DocBook
and store the DocBook representation in the ProjectDescriptor using a specialized DocBookDescriptor.

.. important::

   phpDocumentor needs a way to know which doc files it needs to interpret; these formats do not provide a
   TOC mechanism by default.

For reST we have additional requirements: try to simulate Sphinx as much as possible.

As with normal PHP source file handling, these entries should be stored in the cache and incremental processing applied
by storing the hash of the file.

Transformation phase
~~~~~~~~~~~~~~~~~~~~

To render the documentation in the expected output format we use a dedicated writer class to render the DocBook to
the intended output format (which should be provided to the Query parameter, HTML is the default). An expected
challenge is links between documents and/or source code.