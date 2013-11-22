Inside DocBlocks
================

Overview
--------

If all is well you have read the :doc:`Getting Started<../getting-started/your-first-set-of-documentation>` and got a
basic idea on what a DocBlock is and what you can do with it. In this guide I will repeat some of the bits and then
dive a lot deeper in and discuss the details on what constitutes a DocBlock and what you can do with it.

*An important thing to know is that a DocBlock is always associated with one, and just one, term:`Structural Element`
in PHP; so this may either be files, classes, interfaces, traits, functions, constants, methods and properties.*

Anatomy of a DocBlock
---------------------

A :term:`DocBlock` is in fact the name for a combination of a :term:`DocComment` and a block of the :term:`PHPDoc`
Domain Specific Language. A DocComment is the container that contains documentation, which can be formatted
according to the :doc:`PHPDoc Standard<../references/phpdoc/index>`.

**DocComments**

A DocComment starts with a forward slash and two asterisks (``/**``), which is similar to how you start a multiline
comment, and ends with an asterisk and forward slash (``*/``).
DocComments may be a single line in size but may also span multiple lines, in which case each line must start with an
asterisk. It is customary, and recommended, to align the asterisk when spanning multiple lines.

So, a single line DocComment looks like this::

    /** This is a single line DocComment. */

And a multiline DocComment looks like this::

    /**
     * This is a multi-line DocComment.
     */

**PHPDoc**

Inside DocComments phpDocumentor expects to find a block of text that matches the PHPDoc Standard. Commonly this
consists of three sections:

1. Summary, a short piece of text, usually one line, providing an overview of the functionality contained in the element
   to which this DocBlock is associated.

Summary
~~~~~~~

Description
~~~~~~~~~~~

Tags
~~~~

Types of DocBlocks
------------------

Types
-----

Inheritance
-----------

DocBlock templates
------------------

Read more
---------

* :doc:`../getting-started/your-first-set-of-documentation`
* :doc:`../references/phpdoc/index`
