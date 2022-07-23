Design Document
===============

Goal
----

Our goal with the phpDocumentor documentation is to assist readers in getting running with phpDocumentor within a short
timespan with a minimum of effort. These chapters are designed to become increasingly in-depth, starting with a series
of easy tutorials and how-to's, and ending with references that can be consulted when necessary.

Structure
---------

The documentation is based on the concepts described the Diataxis_ framework.

The concepts in this framework are not directly reflected in the directory structure. Instead, the directory structure
attempts to provide tutorials and how-to guides by going through the journey a new user could take to get to learn
how to work with the project.

The basic structure resembles:

1. What can I do with phpDocumentor? (Features section)
2. When do I write documentation with phpDocumentor (Kinds of Documentation / When to write documentation)
3. How do I create content
3.1. Get up and running (Installation / Usage sections)
3.2. Write useful documentation (Writing Tutorials, Guides and References)
3.3. Make my API Documentation more useful (Making API Documentation more useful)
4. How do I integrate it into my project (Integrating into your site, Extending phpDocumentor)

The next sections are 'down the rabbit hole' explanation and reference sections -it is assumed users only come here to
look things up, if at all.

1. References, anything ranging from cli arguments until docblock tags through to an description of the AST format.
2. Explanations (Going in-depth section), basically contributor/internal documentation

Tools
-----

Our user documentation is written using the `RestructuredText`_ markup style. For our Documentation we eat our own
dogfood and generate it using phpDocumentor 3.

Style Guide
-----------

* All headings should adhere to title case.
* Documents are written in *we*-style; when talking about the project it is we or us and not I.

Outline
-------

.. note::

   This is a revised outline as designed on July 3rd 2022; it is expected that this will become out of date once
   the documentation becomes stable. This chapter will be removed once setup is complete as it is replaced by the
   table of contents. The previous outline is present as inspiration for this one.

- Features
- Installation
- Kinds of Documentation
- When to Write Documentation
- Usage
  - Running phpDocumentor
  - Configuration
  - Scoping what to Include or Exclude
  - As a Github Action
- Writing Tutorials, Guides and References
  - Starting with a Simple Page
  - Writing a Complete Section
  - Describing Relations
  - Including Diagrams
- Making API Documentation More Useful
  - Clarifying Code
  - More Specific Type-Hinting
  - Describing Relations
  - Working with Markers
- Integrating into your site
  - Add your own menu items
  - Adjust styling to match your own
  - Adding or remove pieces
  - Writing your own theme
  - Going headless
- Extending phpDocumentor
  - Adding Custom Tags
  - Adding Custom Directives
- Features
  - Caching
  - Inheritance
- References
  - Command-line arguments
  - Configuration
    - General
    - Guides
    - API Documentation
  - PHPDoc
    - Syntax
  	- Inheritance
    - Types
    - Tags
  	- Inline Tags
  - RestructuredText
    - Directives
  - Twig
    - Functions
    - Filters
  - AST (Headless)
- Going in-depth
  - What's what
  - Architecture
  - The Pipeline
  - Configuration
  - Caching
  - Descriptors
  - Parsing
  - Compiling
  - Linking
  - Transforming
  - Templates
  - Events
  - Search

.. _RestructuredText: https://docutils.sourceforge.io/rst.html
.. _Sphinx:           https://www.sphinx-doc.org/
.. _Diataxis:         https://diataxis.fr/
