Anatomy of a DocBlock
=====================

Introduction
------------

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
auto-completion and by DocBlox to generate API documentation.

This is an example of a DocBlock as it can be encountered:

::

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
     * @author  Mike van Riel <mike.vanriel@unet.nl>
     *
     * @since 1.0
     *
     * @param int    $example  This is an example function/method parameter description.
     * @param string $example2 This is a second example.

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

Long Description
~~~~~~~~~~~~~~~~

Tags
~~~~

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
parent's Long Description using the {@inheritdoc} inline tag.

Each element also inherits a specific set of tags; which ones
depend on the type of element.

The following applies:


-  *ALL*, @author, @version, @copyright
-  *Classes*, @category, @package, @subpackage
-  *Methods*, @param, @return, @throws, @throw
-  *Property*, @var

Please note that @subpackage tags are only inherited if the parent
class has the same @package. Otherwise it is assumed that the
parent class is part of a library which might have a different
structure.

List of Inline Tags
-------------------

Please note that the behavior of tags with headers suffixed with an
asterisk is not yet implemented.

@example\*
~~~~~~~~~~

@id\*
~~~~~

@internal\*
~~~~~~~~~~~

@inheritdoc
~~~~~~~~~~~

@link\*
~~~~~~~

@source\*
~~~~~~~~~

@toc\*
~~~~~~

@tutorial\*
~~~~~~~~~~~

List of Tags
------------

Please note that the behavior of tags with headers suffixed with an
asterisk is not yet implemented; the tag and any contents are
however visible in the documentation.

@abstract\*
~~~~~~~~~~~

@access
~~~~~~~

@api [NEW]
~~~~~~~~~~

@author\*
~~~~~~~~~

@category\*
~~~~~~~~~~~

@copyright
~~~~~~~~~~

@deprecated\*
~~~~~~~~~~~~~

@example\*
~~~~~~~~~~

@final\*
~~~~~~~~

@filesource\*
~~~~~~~~~~~~~

@global\*
~~~~~~~~~

@ignore
~~~~~~~

@internal
~~~~~~~~~

@license
~~~~~~~~

@link
~~~~~

@magic [NEW]
~~~~~~~~~~~~

@method\*
~~~~~~~~~

@name\*
~~~~~~~

@package
~~~~~~~~

@param
~~~~~~

@property
~~~~~~~~~

@property-read
~~~~~~~~~~~~~~

@property-write
~~~~~~~~~~~~~~~

@return
~~~~~~~

@see
~~~~

@since\*
~~~~~~~~

@static\*
~~~~~~~~~

@staticvar\*
~~~~~~~~~~~~

@subpackage
~~~~~~~~~~~

@throws / @throw
~~~~~~~~~~~~~~~~

@todo
~~~~~

@tutorial\*
~~~~~~~~~~~

@uses / @usedby\*
~~~~~~~~~~~~~~~~~

@var
~~~~

@version\*
~~~~~~~~~~


