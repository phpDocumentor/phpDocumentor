Inside DocBlocks
================

Overview
--------

If all is well you have read the :doc:`Getting Started<../getting-started/your-first-set-of-documentation>` and got a
basic idea on what a :term:`DocBlock` is and what you can do with it. In this guide I will repeat some of the bits and
then dive a lot deeper in and discuss the details on what constitutes a DocBlock and what you can do with it.

Anatomy of a DocBlock
---------------------

Using a DocBlock you are able to effectively document your application's API (Application Programming Interface) by
describing the function of, and relations between, elements in your source code, such as classes and methods.

In reality a :term:`DocBlock` is in fact the name for a combination of a, so-called, :term:`DocComment` and a block of
the :term:`PHPDoc` Domain Specific Language (DSL). A DocComment is the container that contains documentation that can
be formatted according to the :doc:`PHPDoc Standard<../references/phpdoc/index>`.

.. important::

   A DocBlock is always associated with one, and just one, :term:`Structural Element` in PHP; so this may either be
   a file, class, interface, trait, function, constant, method, property or variable.

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

Something a little more extensive is the PHPDoc DSL. Inside :term:`DocComments` phpDocumentor, and many other tools with
it, expect to find a block of text that matches the :doc:`PHPDoc Standard<../references/phpdoc/index>`.

Commonly a piece of PHPDoc consists of the following three parts in order of appearance:

Summary
    A short piece of text, usually one line, providing the basic function of the associated element.
Description
    An optional longer piece of text providing more details on the associated element's function. This
    is very useful when working with a complex element.
A series of tags
    These provide additional information in a structured manner. With these tags you can link to other
    elements, provide type information for properties and arguments, and more.

.. important::

   The order of these elements matter; you cannot put a summary or description after the tags as it will be seen as part
   of the last tag.

Summary
~~~~~~~

The summary is a short but effective overview of an element; you can compare it to a slogan or headline.
Summaries are plain and simple, they cannot contain formatting or inline tags (see the next chapter for more information
on this) and can be used to skim through, for example, a list of methods.

The summary can be separated from the description in two ways:

1. With an empty whiteline::

       /**
        * This is a summary
        *
        * This is a description
        */

2. Or by adding a period followed by a new-line::

       /**
        * This is a summary.
        * This is a description
        */

There is not much more to summaries, they are simple and straightforward to use.

Description
~~~~~~~~~~~

This is where the fun starts! A description can be a long text with an elaborate explanation what the associated
element does. The description is *optional*, as there are many elements that are so straightforward that they do
not need a length explanation.

**Even worse**: proper methods are often so simple that a description could be considered overkill!

The nice thing about this description is that you can format your text according to Markdown_, more specifically
`Github-glavoured Markdown`_. Using this format it is easy to make your text bold, add inline code examples or
easily generate links to other sites.

Another nifty feature is that you can use a series of :term:`Inline Tags` to refer to other parts of the documentation
(``{@see}``), inherit the description of a parent (``{@inheritDoc}``) and more. Once you finish reading this guide
you should definitely take a look at the :doc:`../references/phpdoc/inline-tags/index` to see which `Inline Tags` there
are and what they do.

The description can be as long as you would like and ends when a tag is encountered for the first time.

Tags
~~~~

Tags are a type of specialized information (meta-data) about the associated element. At the time of writing of this
guide PHPDoc counts twenty-eight (28) types of tags.

A tag always starts on a new line with an at-sign (@) followed by the name of the tag. Between the start of the line and
the tag's name (including at-sign) there may be one or more spaces or tabs.

The following is an example of a simple tag::

    /**
     * @source
     */

In addition to their name each tag may have arguments that can provide additional context specific for that tag. The
most common example of this is the `@param` tag, with which the argument of a method or function is documented::

    /**
     * @param string $argument1 This is the description.
     */

In the example above we can see that the @param tag features an argument that tells you that the argument with name
``$argument1`` is of type ``string`` and has a description ``This is the description.`` that, in real life, will tell
you the function of that argument.

The best way to discover which options a tag supports is by reading the documentation for that specific tag.

Most tags are associated with a specific element type. So some tags only apply to classes, some only to methods, etc.
The easiest way to see to which element a tag applies is to check the documentation for each tag, or consult the
table in the next chapter.

List of tags
++++++++++++

============== ================ ========================================================================================
Tag            Element          Description
============== ================ ========================================================================================
api            Methods          declares that elements are suitable for consumption by third parties.
author         Any              documents the author of the associated element.
category       File, Class      groups a series of packages together.
copyright      Any              documents the copyright information for the associated element.
deprecated     Any              indicates that the associated element is deprecated and can be removed in a future
                                version.
example        Any              shows the code of a specified example file or, optionally, just a portion of it.
filesource     File             includes the source of the current file for use in the output.
global         Variable         informs phpDocumentor of a global variable or its usage.
ignore         Any              tells phpDocumentor that the associated element is not to be included in the
                                documentation.
internal       Any              denotes that the associated elements is internal to this application or library and
                                hides it by default.
license        File, Class      indicates which license is applicable for the associated element.
link           Any              indicates a relation between the associated element and a page of a website.
method         Class            allows a class to know which ‘magic’ methods are callable.
package        File, Class      categorizes the associated element into a logical grouping or subdivision.
param          Method, Function documents a single argument of a function or method.
property       Class            allows a class to know which ‘magic’ properties are present.
property-read  Class            allows a class to know which ‘magic’ properties are present that are read-only.
property-write Class            allows a class to know which ‘magic’ properties are present that are write-only.
return         Method, Function documents the return value of functions or methods.
see            Any              indicates a reference from the associated element to a website or other elements.
since          Any              indicates at which version the associated element became available.
source         Any, except File shows the source code of the associated element.
subpackage     File, Class      categorizes the associated element into a logical grouping or subdivision.
throws         Method, Function indicates whether the associated element could throw a specific type of exception.
todo           Any              indicates whether any development activity should still be executed on the associated
                                element.
uses           Any              indicates a reference to (and from) a single associated element.
var            Properties
version        Any              indicates the current version of Structural Elements.
============== ================ ========================================================================================

Please see the :doc:`tag reference<../references/phpdoc/tags/index>` for the canonical list of tags and their complete
descriptions.

Annotations
+++++++++++

In addition to the above you might also encounter :term:`Annotations` when viewing DocBlocks. An :term:`Annotation` is
a specialized form of tag that not only documents a specific aspect of the associated element but also influences the
way the application behaves.

Annotations come in various forms, many look exactly like normal tags but some have a more complicated syntax::

    /**
     * @ORM\Entity(repositoryClass="MyProject\UserRepository")
     */

In the example above we demonstrate how you define that a class represents a database entity in Doctrine; as you can see
the tag name is separated into two parts, a namespace and the actual annotation name,

.. important::

   Some annotation libraries support Annotations both with and without a namespace. When given the opportunity use a
   namespace to prevent conflicts with existing tags in the :term:`PHPDoc Standard`.

   When you are using the regular tag syntax it is recommended to prefix the tag with a name representing your
   application or organisation's name and a hyphen. For example::

       phpdoc-event onClick

To read more on annotations I recommend taking a look at the slides for Rafael Dohms' talk on annotations
(http://www.slideshare.net/rdohms/annotations-in-php-they-exist) or view his talk
(http://protalk.me/annotating-with-annotations).

Related topics
--------------

* :doc:`types`, for details on which types are supported by phpDocumentor.
* :doc:`inheritance`, to read how DocBlocks inherit information from elements in superclasses.
* :doc:`../getting-started/your-first-set-of-documentation`, for an introduction in writing DocBlocks.
* :doc:`../references/phpdoc/index`, for a complete, and more elaborate, reference on the syntax and capabilities of
  DocBlocks.

.. _Github-glavoured Markdown: https://help.github.com/articles/github-flavored-markdown
.. _Markdown:                  http://daringfireball.net/projects/markdown/
