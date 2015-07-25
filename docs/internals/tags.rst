Tags
====

.. important::

   This is a design document for the way tags should work, not all the functionality described in this document is
   implemented yet.

.. important:: This is a working draft, the contents have not been finished yet

Introduction
------------

A Tag is, from the perspective of phpDocumentor, a syntactical element comparable to Functions, Arguments and Classes.
As such it uses the same mechanism for creation, filtering and validation.

Theory of Operation
-------------------

#. Tags may be interpreted and separated in parts by the ReflectionDocBlock component, for example::

       The @author tag is separated into a tagName, Author, E-mail address, Role and Description by the
       'phpDocumentor\Reflection\DocBlock\Tag\AuthorTag' class.

   This is done in the ReflectionDocBlock component during the parsing of a source file. If there is no reflector for
   a specific tag then the generic ``phpDocumentor\Reflection\DocBlock\Tag`` reflector is used to separate a tag into
   a tagName and Description.

#. Each individual tag is represented by a :term:Descriptor, if there is no Descriptor available then the generic
   ``phpDocumentor\Descriptor\TagDescriptor`` class is used. The TagDescriptors are populated by an :term:Assembler
   class that is invoked by the ProjectDescriptorBuilder after the parsing process.

#. Writers capable of rendering templates, such as Twig and XSL, include a tag definition that determines where to
   include sub-templates containing tag definitions

   .. important::

      Do not create a separate template file for each tag but instead make one file per plugin; this will reduce I/O
      and save performance.

   For example; the Core plugin may have a file ``[plugin]/templates.html.twig`` that defines additional blocks for
   the Twig writer, which is added by the Service Provider of that plugin.

   For more information on how to accomplish this with a specific plugin, see the documentation for that specific
   Writer.

Tags in plugins
---------------

To add a custom tag in a plugin it you should add the following:

#. Reflector class
#. Descriptor class
#. Assembler class
#. Tag template block

.. note::

   You may be wondering why so much code is needed to introduce a new tag; this is because phpDocumentor is built to
   support different input types to create a Descriptor with. In this case we have the Reflector as input and the
   Assembler is the component that constructs a valid Descriptor from that Reflection information.

   In future versions phpDocumentor may have an additional input that complements the Reflector library and it would
   need another Assembler capable of dealing with transforming that input into a Descriptor.

Variant approach: Quick and dirty
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

#. Write Descriptor
#. In ServiceProvider, link a matcher to a closure assembler that constructs your Descriptor, parses your tag and sets
   the appropriate properties of the Descriptor.
#. Tag template block

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
   
Filtering tags
--------------

Validating tags
---------------
