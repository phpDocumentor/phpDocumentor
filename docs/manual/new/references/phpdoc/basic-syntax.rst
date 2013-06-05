Basic Syntax
============

In this chapter will an overview be given of the syntax used with
DocBlocks. The precise effect of a tag including examples are
provided in different chapters which are accessible via this
document.

What is a DocBlock?
-------------------

A docblock is a special type of comment that can provide verbose
information about an element in your code.

The information provided in this type of comment can be used by
developers to gain understanding of the function of a given
element; but it is also used by IDEs to provide (among others)
auto-completion and by phpDocumentor to generate API documentation.

This is an example of a DocBlock as it can be encountered:

.. code-block:: php
   :linenos:

    /**
     * This is the short description for a DocBlock.
     *
     * This is the long description for a DocBlock. This text may contain
     * multiple lines and even some _markdown_.
     *
     * * Markdown style lists function too
     * * Just try this out once
     *
     * The section after the long description contains the tags; which provide
     * structured meta-data concerning the given element.
     *
     * @author  Mike van Riel <mike.vanriel@naenius.com>
     *
     * @since 1.0
     *
     * @param int    $example  This is an example function/method parameter description.
     * @param string $example2 This is a second example.
     */

Which elements can have a DocBlock
----------------------------------

:term:`Structural Elements` can all be preceeded by a DocBlock. The following
elements are counted as such:

    * namespace
    * require(_once)
    * include(_once)
    * class
    * interface
    * trait
    * function (including methods)
    * property
    * constant
    * variables, both local and global scope.

A more detailed description of what :term:`Structural Elements` are and how
DocBlocks apply to them can be found in the :doc:`../introduction/definitions`.

Sections
--------

A DocBlock roughly exists of 3 sections:


1. Short Description; a one-liner which globally states the
   function of the documented element.
2. Long Description; an extended description of the function of the
   documented element; may contain markup and inline tags.
3. Tags; a series of descriptors for properties of this element;
   such as @param and @return.

Short Description
~~~~~~~~~~~~~~~~~

The short description is used to give an impression of the function of the
documented element. This can be used in overviews to allow the user to skim
the documentation in search of the required template.

Short descriptions should always end in either a full stop, or 2 consecutive new
lines. If it is not closed like this then any long description will be
considered as part of the short description.

.. NOTE::

    A full stop means that the dot needs to be succeeded by a new line or other
    type of whitespace. This way it is possible to mention a version number,
    for example, without stopping the short description.

Long Description
~~~~~~~~~~~~~~~~

The long description contains concise information about the function of the
documented element. It is allowed, and encouraged, to use Markdown markup to
apply styling.

The following list has examples of types of information that can be contained
in a long description:

* Explanation of algorithms
* Code examples
* Array specification
* Relation to other elements
* License information (in the case of file DocBlocks)

Long descriptions can also contain inline tags. These are special annotations
that can be substituted for a specialized type of information (such as {@link}).
Inline tags are always surrounded by braces.

A complete listing is provided in :doc:`inline-tag-reference`.

Tags
~~~~

Tags represent meta-data with which IDEs, external tooling or even the
application itself know how to interpret an element.

phpDocumentor understands and uses (almost) all types supported by phpDocumentor.
A complete listing is provided in :doc:`tag-reference`.

In addition phpDocumentor is able to understand, and link to, the annotations of
Doctrine2.

Inheritance
-----------

Docblocks automatically inherits the Short and Long description of
an overridden, extended or implemented element.

For example: if Class B extends Class A and it has an empty
DocBlock defined, then it will have the same Short description and
Long description as Class A. No DocBlock means that the 'parent'
DocBlock will not be overridden and an error will be thrown during
parsing.

This form of inheritance applies to any element that can be
overridden, such as Classes, Interfaces, Methods and Properties.
Constants and Functions can not be overridden in and thus do not
have this behavior.

Please note that you can also augment a Long Description with its
parent's Long Description using the {:doc:`inline-tags/inheritdoc`} inline tag.

Each element also inherits a specific set of tags; which ones
depend on the type of element.

The following applies:

======================== ============================================================================
Elements                 Inherited tags
======================== ============================================================================
*Any*                    :doc:`tags/author`, :doc:`tags/version`, :doc:`tags/copyright`
*Classes and Interfaces* :doc:`tags/category`, :doc:`tags/package`, :doc:`tags/subpackage`
*Methods*                :doc:`tags/param`, :doc:`tags/return`, :doc:`tags/throws`
*Properties*             :doc:`tags/var`
======================== ============================================================================

Please note that @subpackage tags are only inherited if the parent
class has the same @package. Otherwise it is assumed that the
parent class is part of a library which might have a different
structure.
