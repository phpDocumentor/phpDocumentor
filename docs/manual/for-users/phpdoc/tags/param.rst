@param
======

The @param tag is used to document a single argument of a function or method.

Syntax
------

    @param [:term:`Type`] [name] [<description>]

Description
-----------

With the @param tag it is possible to document the type and function of a
single argument of a function or method. When provided it MUST contain a
:term:`Type` to indicate what is expected; the description on the other hand is
OPTIONAL yet RECOMMENDED in case of complicated structures, such as associative
arrays.

The @param tag MAY have a multi-line description and does not need explicit
delimiting.

It is RECOMMENDED when documenting to use this tag with every function and
method. Exceptions to this recommendation are:

This tag MUST NOT occur more than once per argument in a :term:`PHPDoc` and is
limited to :term:`Structural Elements` of type method or function.

Effects in phpDocumentor
------------------------

:term:`Structural Elements` of type method or function, that are tagged with the
@param tag, will have additional information in the section regarding Arguments.

If the return :term:`Type` is a class that is documented by phpDocumentor, then
a link to that class' documentation is provided.

.. note::

   phpDocumentor supports @param tags which omit the name, this is
   NOT RECOMMENDED but provided for compatibility with existing projects.

.. note::

   phpDocumentor will try to analyze correct usage and presence of the @param
   tag; as such it will provide error information in the following scenarios:

   * An @param is provided but no argument was found matching the @param.
   * The name of the @param does not match the name of argument at the same
     position.
   * A mismatch between the type hint, if present, and the type declaration was
     detected.
   * The type declaration is *type*; this is an invalid type and often provided
     by IDEs.

Examples
--------

.. code-block:: php
   :linenos:

    /**
     * Counts the number of items in the provided array.
     *
     * @param mixed[] $array Array structure to count the elements of.
     *
     * @return int Returns the number of elements.
     */
    function count(array $items)
    {
        <...>
    }
