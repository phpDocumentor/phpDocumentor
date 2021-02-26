Guide generation
================

Guide generation is phpDocumentor's implementation for converting hand-written documentation into a static page
integrated with the API Documentation. These share each other's Table of Contents in order for them to link to one
another.

The design for the Guide Generation is based on the premise that we could support multiple input formats
(RestructuredText, Markdown, Textile, etc); but in the first version RestructuredText is the only supported input
format.

Attribution
-----------

The code for rendering guides using RestructuredText-based documentation is based on, and with permission of,
Ryan Weaver his work on the `docs-builder`_ package, combined with the RST-Parser by Doctrine (which is based off that
of Gregwar).

Architecture
------------

.. important::

   While writing this documentation set, the architecture is in flux as Mike is refactoring Doctrine's implementation
   to match the architecture of phpDocumentor. During this process, the aim is to keep this chapter up to date but
   we advice to double-check the implementation against this chapter.

The architecture of the Guides implementation revolves mainly around these 5 concepts:

1. Kernel - the core orchestrator for parsing and rendering RestructuredText into the requested output format.
2. Documents & Nodes - the broken down composition of a document, similar to an AST for code.
3. Directives - Specialized sub-parsers that know how to deconstruct RestructuredText Directives into Nodes.
4. (Output) Formats - Specialized functionality to render the parsed documents into a requested output format,
   such as HTML or LaTeX (for PDF generation).
5. Renderers - services used to convert Nodes into pieces of output format, such as HTML

Kernel
~~~~~~

Documents & Nodes
-----------------

Directives
~~~~~~~~~~

(Output) Formats
~~~~~~~~~~~~~~~~

Renderers
~~~~~~~~~

Read More
---------

- `Writing Directives <./guides/writing-directives>`_

.. _docs-builder: https://github.com/ryanweaver/docs-builder
