@throws
=======

The ``@throws`` tag is used to indicate whether *Structural Elements*
throw a specific type of `Throwable`_ (`exception or error`_).

Syntax
------

.. code-block::

    @throws [Type] [<description>]

Description
-----------

The ``@throws`` tag MAY be used to indicate that *Structural Elements* throw
a specific type of error.

The :doc:`Type <../types>` provided with this tag MUST represent an object
that implements the `Throwable`_ interface, such as an `Error`_, `Exception`_
or any subclass thereof.

This tag is used to present in your documentation which error COULD occur and
under which circumstances. It is RECOMMENDED to provide a description that
describes the reason an exception is thrown.

It is also RECOMMENDED that this tag occurs for every occurrence of an
exception, even if it has the same type. By documenting every occurrence a
detailed view is created and the consumer knows for which errors to check.

Effects in phpDocumentor
------------------------

PhpDocumentor shows a listing of errors thrown by the documented element and
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
     * @throws InvalidArgumentException if the provided argument is
     *                                  not of type 'array'.
     *
     * @return int Returns the number of elements.
     */
    function count($items)
    {
        <...>
    }

.. _Throwable:           https://www.php.net/class.throwable
.. _exception or error:  https://www.php.net/language.exceptions
.. _Error:               https://www.php.net/class.error
.. _Exception:           https://www.php.net/class.exception
