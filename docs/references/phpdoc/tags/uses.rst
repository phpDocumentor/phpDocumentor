@uses & @used-by
================

The @uses tag indicates a reference to (and from) a single associated :term:`Structural Elements`.

Syntax
------

    @uses [:term:`FQSEN`] [<description>]

Description
-----------

The @uses tag is used to describe a consuming relation between the current element and any other of the
:term:`Structural Elements`.

@uses is similar to @see (see the documentation for @see for details on format and structure). The @uses tag differs
from @see in that @see is a one-way link, meaning the documentation containing a @see tag contains a link to other
Structural Elements or URI's but no link back is implied.

Documentation generators SHOULD create a @used-by tag in the documentation of the receiving element that links back to
the element associated with the @uses tag.

When defining a reference to another :term:`Structural Elements` you can refer to a specific element by providing the
:term:`FQSEN`.

The @uses tag COULD have a description appended to provide more information regarding the usage of the destination
element.

Effects in phpDocumentor
------------------------

:term:`Structural Elements` tagged with the @uses tag will show a link in their description. If a description is
provided with the tag then this will be shown as additional information.

In addition, phpDocumentor generates a @used-by tag with the receiving element (if documented) referring back to the
calling element.

Examples
--------

.. code-block:: php
   :linenos:

    /**
     * @uses MyClass::$items to retrieve the count from.
     *
     * @return integer Indicates the number of items.
     */
    function count()
    {
        <...>
    }