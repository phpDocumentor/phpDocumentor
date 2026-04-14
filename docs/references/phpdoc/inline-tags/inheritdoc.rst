@inheritdoc
===========

The ``{@inheritDoc}`` inline tag is used to indicate that the documentation
of a parent *Structural Element* should be reused on a child element that
overrides it.

Syntax
------

.. code-block::

    {@inheritDoc}

Description
-----------

When a *Structural Element* overrides another, phpDocumentor can reuse the
documentation from the parent element so you do not have to repeat it.
Most of that inheritance is performed automatically, see
:doc:`../../../guides/inheritance` for the full list of elements and tags
that are inherited.

The ``{@inheritDoc}`` inline tag makes this intent explicit in the source
code. Even though it uses inline-tag syntax, in phpDocumentor it is
typically written as the sole content of a summary to signal that the
DocBlock body should be inherited from the parent element.

Effects in phpDocumentor
------------------------

phpDocumentor applies inheritance as follows:

* When the *summary* of a DocBlock is missing or consists only of
  ``{@inheritDoc}`` (matched case-insensitively), phpDocumentor uses the
  summary of the parent element instead.
* When the *description* of a DocBlock is missing, phpDocumentor uses the
  description of the parent element.

Summary and description are resolved independently: a child DocBlock may
keep its own summary while inheriting the description from the parent, or
the other way around.

If no parent element can be resolved (for example on a root class or on an
element that does not override anything), nothing is inherited and the
DocBlock is rendered as written.

Examples
--------

Reuse the summary and description of the parent method:

.. code-block:: php
   :linenos:

    class ParentClass
    {
        /**
         * This is the parent summary.
         *
         * This is the parent description.
         */
        public function aMethod()
        {
        }
    }

    class ChildClass extends ParentClass
    {
        /**
         * {@inheritDoc}
         */
        public function aMethod()
        {
        }
    }

Keep the child summary but inherit the parent's description by leaving
the description empty:

.. code-block:: php
   :linenos:

    class ChildClass extends ParentClass
    {
        /**
         * This is the child summary.
         */
        public function aMethod()
        {
        }
    }

Related topics
--------------

* :doc:`../../../guides/inheritance`, for a full description of how
  inheritance of DocBlocks works in phpDocumentor.
