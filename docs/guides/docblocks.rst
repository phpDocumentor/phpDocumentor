Inside DocBlocks
================

Overview
--------

If all is well you have read the :doc:`Getting Started<../getting-started/your-first-set-of-documentation>` and got a
basic idea on what a DocBlock is and what you can do with it. In this guide I will repeat some of the bits and then
dive a lot deeper in and discuss the details on what constitutes a DocBlock and what you can do with it.

Anatomy of a DocBlock
---------------------

Using a DocBlock you are able to effectively document your application's API (Application Programming Interface) by
describing the function of, and relations between, elements in your source code, such as classes and methods.

In reality a :term:`DocBlock` is in fact the name for a combination of a, so-called, :term:`DocComment` and a block of
the :term:`PHPDoc` Domain Specific Language (DSL). A DocComment is the container that contains documentation that can
be formatted according to the :doc:`PHPDoc Standard<../references/phpdoc/index>`.

.. important::

   A DocBlock is always associated with one, and just one, term:`Structural Element` in PHP; so this may either be
   files, classes, interfaces, traits, functions, constants, methods and properties.

**DocComments**

A DocComment starts with a forward slash and two asterisks (``/**``), which is similar to how you start a multiline
comment but with an additional asterisk, and ends with an asterisk and forward slash (``*/``).
DocComments may be a single line in size but may also span multiple lines, in which case each line must start with an
asterisk. It is customary, and recommended, to align the asterisks vertically when spanning multiple lines.

So, a single line DocComment looks like this::

    /** This is a single line DocComment. */

And a multiline DocComment looks like this::

    /**
     * This is a multi-line DocComment.
     */

That is easy, right?

**PHPDoc**

Something a little more extensive is the PHPDoc DSL. Inside DocComments phpDocumentor, and many other tools with it,
expect to find a block of text that matches the :doc:`PHPDoc Standard<../references/phpdoc/index>`.

Commonly a piece of PHPDoc consists of three parts:

1. Summary - a short piece of text, usually one line, providing the basic function of the associated element.
2. Description - an optional longer piece of text providing more details on the associated element's function. This
   is very useful when working with a complex element.
3. A series of tags - these provide additional information in a structured manner. With these tags you can link to other
   elements, provide type information for properties and arguments, and more.

Summary
~~~~~~~

The summary is a short but effective overview of an element; you can compare it to a slogan or headline.

Description
~~~~~~~~~~~

Tags
~~~~

Types of DocBlocks
------------------

Inheritance
-----------

DocBlock templates
------------------

Read more
---------

* :doc:`../getting-started/your-first-set-of-documentation`
* :doc:`../references/phpdoc/index`
