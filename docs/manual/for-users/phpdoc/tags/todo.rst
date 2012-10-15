@todo
=====

The @todo tag is used to indicate whether any development activities should
still be executed on associated :term:`Structural Elements`.

Syntax
------

    @todo [description]

Description
-----------

The @todo tag is used to indicate that an activity surrounding the associated
:term:`Structural Elements` must still occur. Each tag MUST be accompanied by
a description that communicates the intent of the original author; this could
however be as short as providing an issue number.

Effects in phpDocumentor
------------------------

Besides showing the tag information with the associated documentable element
phpDocumentor will also generate a report containing all todo items with
references to their elements.

.. hint::

   To document TODO items inline in the code, consider using the TODO markers,
   see the chapter :doc:`../../reporting/markers` for more information.

Examples
--------

.. code-block:: php
   :linenos:

    /**
     * Counts the number of items in the provided array.
     *
     * @todo add an array parameter to count
     *
     * @return int Returns the number of elements.
     */
    function count()
    {
        <...>
    }
