Compiler
========

phpDocumentor's Transformer component encapsulates the following processes:

Compiler
    Executes a series of compiler steps to transform the ProjectDescriptor (and its children) to a set of
    :term:`artifacts`. phpDocumentor provides a few compiler steps by default, such as the linker and transformer.
    Consumers can add additional compiler passes using Service Providers.

Linker
    The cached ProjectDescriptor only contains textual references to other elements (such as a parent class). The
    linker subsequently creates a series of default indexes and replaces textual references with Class Descriptor
    objects.
    The linker is one of the first compiler passes in the compiler.

Transformer
    The transformer process is responsible for interpreting a template and executing transformations contained in that
    template. The end-result of this process is that a series of :term:`artifacts` is generated, such a PDF or HTML
    documentation.

Compiler
--------

As described in the introduction of this chapter is the compiler responsible for creating a set of :term:`artifacts`
and all pre-processing associated with that.

The compiler is an instance of the ``SplPriorityQueue`` that contains a series of Compiler Passes (see the
``phpDocumentor\Transformer\CompilerPassInterface``). Every inserted pass should be assigned a priority, though
duplicates are not considered an issue.

.. important::

   The default behaviour of the ``SplPriorityQueue`` class is to remove elements during iteration. As such it is not
   possible to walk back the tree during the processing of a compiler pass; each pass should be atomic in function or
   catefully positioned in the queue.

Activity diagram
~~~~~~~~~~~~~~~~

.. uml::

    start

    :#d1ed57:Create compiler;
    :#d1ed57:Retrieve compiler passes and insert into compiler;
    :#d1ed57:Order compiler passes based on priority;

    while (Compiler passes remaining?)

        :#d1ed57:Execute Compiler pass' functionality;

    endwhile

    stop

.. note:: the ordering of compiler passes is handled by the ``SplPriorityQueue`` class.

Linker
------

The linker is a compiler pass that is responsible for replacing Fully Qualified Structural Element Names (FQSENs) in
the Project Descriptor's Files section (and its children) with an `object alias`_ to the Descriptor object belonging to
said FQSEN.

The linker is needs to be fed with information on how to find a FQSEN and optionally which type of FQSEN is expected.
This can be done by providing *linking rules* that informs the linker which field for a given class to attempt to link.

.. warning::

   Namespace FQSENs may be duplicate to classes, traits, interfaces and constants. Any time a FQSEN is encountered that
   can be interpreted as any of these types will the following search order be used: Class, Interface, Trait,
   Namespace.

   If a FQSEN is only related to a specific type of element than specifying which one will speed up the application as
   it reduces lookups.

An example of *linking rule* can be:

    For class ``\phpDocumentor\Descriptor\ClassDescriptor`` should the field *ParentClass* be replaced with a FQCN
    pointing to a(nother) ClassDescriptor.

The above example will try to retrieve the ParentClass field using the getter ``getParentClass``, try to find a
ClassDescriptor with the FQSEN contained in the ParentClass field and set that using the method``setParentClass``.

.. important::

   Because a *linking rule* depends on finding the Descriptor for a specific FQSEN it is important to execute a
   compiler pass before this pass that builds an index with all structural elements.


If the ParentClass field contains anything other than a ClassDescriptor, or a FQCN pointing to an object other than a
ClassDescriptor then an error should be logged that an invalid reference was found. This means that an empty string or
an string referring to no object at all is still allowed (a string nor referring to an object may point to a class
external to this project).

Please note that linking rules should and do cascade. The linker does not do anything it isn't told. As such the
following pseudo rule is required to even scan a FileDescriptor:

    For class ``\phpDocumentor\Descriptor\ProjectDescriptor`` should the field *Files* be scanned.

The ProjectDescriptor is the only object that is automatically scanned, any other object should be covered by one or
more *linking rules*.

Activity Diagram
~~~~~~~~~~~~~~~~

.. uml::

    start

    :#d1ed57: Load linking rules;

    while (linking rules for ProjectDescriptor remaining?)
        :#d1ed57: Find target;
        if (rule is scan) then (yes)
            :#d1ed57: find applicable linking rules based\non class name;
            :#d1ed57: apply linking rules for target;
        else (no)
            if (rule is replace) then (yes)
                :#d1ed57: Retrieve value using getter;
                if (value is FQSEN) then (yes)
                :#d1ed57: Find FQSEN's Descriptor(s) in index;
                    if (Descriptor's type matches limitation or no limitation) then (yes)
                        :#d1ed57: Sort Descriptors in order\nof precedence;
                        :#d1ed57: Write first Descriptor to field\n using setter;
                    else (no)
                        if (Descriptor type does not match limitation) then (yes)
                            :#d1ed57: record error;
                        endif
                    endif
                endif
            endif
        endif
    endwhile

    stop

.. _`object alias`: http://php.net/manual/en/language.oop5.references.php
