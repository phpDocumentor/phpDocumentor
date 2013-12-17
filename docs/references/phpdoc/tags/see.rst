@see
====

The @see tag indicates a reference from the associated
:term:`Structural Elements` to a website or other :term:`Structural Elements`.

Syntax
------

    @see [URI | :term:`FQSEN`] [<description>]

Description
-----------

The @see tag can be used to define a reference to other
:term:`Structural Elements` or to an URI.

When defining a reference to another :term:`Structural Elements` you can provide
a specific element by appending a double colon and providing the name of that
element (also called the :term:`FQSEN`).

A URI MUST be complete and well-formed as specified in
`RFC2396 <http://www.ietf.org/rfc/rfc2396.txt>`_.

The @see tag SHOULD have a description appended to indicate the type of
reference defined by this occurrence.

Effects in phpDocumentor
------------------------

:term:`Structural Elements`, or inline text in a long description, tagged with
the @see tag will show a link in their description. If a description is
provided with the tag then this will be used as link text instead of the URL
itself.

Examples
--------

.. code-block:: php
   :linenos:

    /**
     * @see http://example.com/my/bar Documentation of Foo.
     * @see MyClass::$items           For the property whose items are counted.
     * @see MyClass::setItems()       To set the items for this collection.
     *
     * @return integer Indicates the number of items.
     */
    function count()
    {
        <...>
    }
