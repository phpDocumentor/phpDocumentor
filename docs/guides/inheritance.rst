Inheritance
===========

phpDocumentor is capable of inheriting a large amount of information from elements in superclasses and
super-interfaces that have been overridden and in the case of classes and interfaces even from a superclass itself.

For ease of reading this document has been separated into three parts:

* Classes and interfaces
* Properties
* Methods

In each of these chapters we will describe how information is inherited for that specific element. If you read
carefully then you will notice that they differ little, mostly varying in which tags are inherited and how each tag is
influenced by a super-element.

.. important::

   Interfaces and classes are treated equally with regards to inheritance, as such whenever we refer to a class we also
   mean an interface to make the text easier on the eyes.

The inheritDoc tag
------------------

Before we discuss how the documentation for each element is inherited it is interesting to point out the inheritDoc tag.
The inline tag ``{@inheritDoc}`` is used in a description to import the description of a parent element, even if the
child element already has a description.

So let us look at an example. In the following code block we re-define (override) a method of an imaginary superclass::

    /**
     * This is the summary.
     *
     * This is the description specific to the redefined method. {@inheritDoc} And this is another
     * part specific to the redefined method.
     */
    public function aMethod()
    {
    }

Now suppose that the overridden method has the description ``This is the description specific to the overridden
method.``; the description of the re-defined method will be::

    This is the description specific to the redefined method. This is the description specific to the overridden
    method. And this is another part specific to the redefined method.

As you can see, the two descriptions have been combined into one, where the overridden element's description has been
inserted in the location of the ``{@inheritDoc}`` inline tag.

.. important::

   Currently some applications have DocBlocks containing just the ``{@inheritDoc}`` inline tag to indicate that their
   complete contents should be inherited. This usage breaks with the PHPDoc Standard as summaries cannot contain inline
   tags and inheritance is automatic; you do not need to define a special tag for it.

   However, it does make clear that an element has been explicitly documented (and thus not forgotten). As such we are
   working to include a new (normal) tag in the PHPDoc Standard ``@inheritDoc`` that will serve that purpose.

Classes and interfaces
----------------------

Perhaps the simplest of all elements, because a DocBlock for a class makes full use of the object-oriented principles
that PHP offers and inherits the following information from the superclass (unless overridden):

* Summary
* Description
* The following tags:

  * author
  * copyright
  * package
  * subpackage
  * version

As hinted at in the opening text of this chapter each of the above will only be inherited if the child's DocBlock does
not have the inherited element. So, for example, if the DocBlock of a subclass has a summary then it will not receive
the superclass' summary.

The ``@subpackage`` tag will only be inherited if the parent's ``@package`` matches the ``@package`` tag of the
subclass; otherwise subpackages would 'bleed' through into other packages where they are not desired.

Properties
----------

Inheritance for properties functions similar to classes and interfaces. When a superclass of the current class contains
a property with the same name (hence, this property is re-defined) then the following information is inherited from
that overridden property:

* Summary
* Description
* The following tags:

  * author
  * copyright
  * version
  * var

As with classes, each of the above will only be inherited if the redefined property's DocBlock does not have the
element that is to be inherited. So, for example, if the DocBlock of the redefined property has a summary then it will
not receive the overridden property's summary.

Methods
-------

Inheritance for methods functions similar to classes and interfaces. When a superclass of the current class contains
a method with the same name (hence, this method is re-defined) then the following information is inherited from
that overridden method:

* Summary
* Description
* The following tags:

  * author
  * copyright
  * version
  * param
  * return
  * throws

As with classes, each of the above will only be inherited if the redefined method's DocBlock does not have the
element that is to be inherited. So, for example, if the DocBlock of the redefined method has a summary then it will
not receive the overridden method's summary.

Related topics
--------------

* :doc:`../references/phpdoc/inheritance`, for a complete, and more elaborate, reference on inheritance.
* :doc:`../references/phpdoc/tags/inline/inheritdoc`, for a full description of the ``@inheritDoc`` inline tag.
