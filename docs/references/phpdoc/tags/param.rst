@param
======

The ``@param`` tag is used to document a single `argument of a function or method`_.

Syntax
------

.. code-block::

    @param [Type] [name] [<description>]

Description
-----------

With the ``@param`` tag it is possible to document the :doc:`Type <../types>`
and function of a single `argument of a function or method`_. When provided it
MUST contain a :doc:`Type <../types>` to indicate what is expected.
The description is OPTIONAL yet RECOMMENDED, for instance, in case of
complicated structures, such as associative arrays.

The ``@param`` tag MAY have a multi-line description and does not need explicit
delimiting.

It is RECOMMENDED when documenting to use this tag with every function and
method.

This tag MUST NOT occur more than once per argument in a PHPDoc and is
limited to *Structural Elements* of type method or function.

Effects in phpDocumentor
------------------------

*Structural Elements* of type method or function, that are tagged with the
``@param`` tag, will have additional information in the section regarding Parameters.

If the parameter *Type* is a class that is documented by phpDocumentor,
then a link to that class' documentation is provided.

.. note::

   phpDocumentor supports ``@param`` tags which omit the name, this is
   NOT RECOMMENDED but provided for compatibility with existing projects.

.. note::

   phpDocumentor will try to analyze correct usage and presence of the ``@param``
   tag; as such it will provide error information in the following scenarios:

   * A ``@param`` is provided but no argument was found matching the ``@param``.
   * The name of the ``@param`` does not match the name of argument at the same
     position.
   * A mismatch between the type hint, if present, and the type declaration was
     detected.
   * The type declaration is ``type``; this is an invalid type and often provided
     by IDEs.

Examples
--------

.. code-block:: php
   :linenos:

    /**
     * Counts the number of items in the provided array.
     *
     * @param mixed[] $items     Array structure to count the elements of.
     * @param bool    $recursive Optional. Whether or not to recursively
     *                           count elements in nested arrays.
     *                           Defaults to `false`.
     *
     * @return int Returns the number of elements.
     */
    function count(array $items, bool $recursive = false)
    {
        <...>
    }


.. _argument of a function or method: https://www.php.net/functions.arguments
