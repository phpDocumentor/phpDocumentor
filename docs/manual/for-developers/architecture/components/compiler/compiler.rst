Compiler
========

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
----------------

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

