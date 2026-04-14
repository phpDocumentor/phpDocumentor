@return
=======

The ``@return`` tag is used to document the `return value of functions or methods`_.

Syntax
------

.. code-block::

    @return [Type] [<description>]

Description
-----------

With the ``@return`` tag it is possible to document the return :doc:`Type <../types>`
of a function or method. When provided it MUST contain a :doc:`Type <../types>`
to indicate what is returned; the description on the other hand is OPTIONAL yet
RECOMMENDED, for instance, in case of complicated return structures, such as
associative arrays.

The ``@return`` tag MAY have a multi-line description and does not need explicit
delimiting.

It is RECOMMENDED to use this tag with every function and
method.
Exceptions to this recommendation, as defined by the Coding Standard of any
individual project, MAY be:

1. **constructors**, the ``@return`` tag MAY be omitted here, in which case
   ``@return self`` is implied.
2. **functions and methods without a `return` value**, the ``@return`` tag MAY be
   omitted here, in which case ``@return void`` is implied.

This tag MUST NOT occur more than once in a PHPDoc and is limited to
*Structural Elements* of type method or function.

The ``$this`` keyword
~~~~~~~~~~~~~~~~~~~~~

In addition to the regular :doc:`Types <../types>`, the ``@return`` tag accepts
the ``$this`` keyword to denote that the method returns the current object
instance itself. It is commonly used to document `fluent interfaces`_ where
setters may return ``$this`` so that calls can be chained:

.. code-block:: php
   :linenos:

    /**
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

Unlike ``self`` (an instance of the class where the method is declared) and
``static`` (an instance of the late-static-bound class), ``$this`` specifically
refers to the exact instance on which the method was called, which is the
semantics most IDEs rely on to offer auto-completion on chained calls.

The ``$this`` notation is only meaningful in the context of a ``@return`` tag;
using it in other tags or types is not part of the PHPDoc Standard.

Effects in phpDocumentor
------------------------

*Structural Elements* of type method or function, that are tagged with the
``@return`` tag, will have an additional section *Returns* in their content
description that shows the return *Type* and description.

If the return *Type* is a class that is documented by phpDocumentor, then a link
to that class' documentation is provided.

Examples
--------

Singular type:

.. code-block:: php
   :linenos:

    /**
     * @return int Indicates the number of items.
     */
    function count()
    {
        <...>
    }

Function can return either of two types:

.. code-block:: php
   :linenos:

    /**
     * @return string|null The label's text or null if none provided.
     */
    function getLabel()
    {
        <...>
    }

.. _return value of functions or methods: https://www.php.net/functions.returning-values
.. _fluent interfaces: https://en.wikipedia.org/wiki/Fluent_interface
