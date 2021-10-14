Guide generation
================

.. versionadded:: 3.1

   The Guide component was introduced as an experimental feature in phpDocumentor 3.1.

.. important::

   This feature is EXPERIMENTAL. None of the features nor the API should be considered stable and
   may change without notice. There is no backwards compatibility promise for this part of the code
   as it is highly in development.

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

.. toctree::

   parser
   writing-directives

.. _docs-builder: https://github.com/ryanweaver/docs-builder
