@throws
=======

The @throws tag is used to indicate whether :term:`Structural Elements` could
throw a specific type of exception.

Syntax
------

    @throws [:term:`Type`] [<description>]

Description
-----------

The @throws tag MAY be used to indicate that :term:`Structural Elements` could
throw a specific type of error.

The type provided with this tag MUST represent an object of the class Exception
or any subclass thereof.

This tag is used to present in your documentation which error COULD occur and
under which circumstances. It is RECOMMENDED to provide a description that
describes the reason an exception is thrown.

It is also RECOMMENDED that this tag occurs for every occurrance of an
exception, even if it has the same type. By documenting every occurance a
detailed view is created and the consumer knows for which errors to check.

Effects in phpDocumentor
------------------------

phpDocumentor shows a listing of errors thrown by the documented element and
allows readers to click-through to the given exception type if it is present in
the project.

Examples
--------

.. code-block:: php
   :linenos:

    /**
     * Counts the number of items in the provided array.
     *
     * @param mixed[] $array Array structure to count the elements of.
     *
     * @throws InvalidArgumentException if the provided argument is not of type
     *     'array'.
     *
     * @return int Returns the number of elements.
     */
    function count($items)
    {
        <...>
    }
