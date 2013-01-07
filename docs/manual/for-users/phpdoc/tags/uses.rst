@uses & @used-by
================

.. note::

   The contents for this chapter requires a review. Why not help us and
   contribute it at
   https://github.com/phpDocumentor/phpDocumentor2/tree/develop/docs/manual

The @uses tag indicates a reference to (and from) a single associated :term:`Structural Elements`.

Syntax
------

    @uses [URI | :term:`Type` | :term:`FQSEN`] [<description>]

Description
-----------

The @uses tag may be used to document any element (global variable, include, page, class, function, define, method, variable)

@uses only displays links to element documentation. If you want to display a hyperlink, use @link or inline {@link}

@uses is very similar to @see (see the documentation for @see for details on format and structure). The @uses tag only differs from @see in that @see is a one-way link, meaning the documentation containing a @see tag contains a link to other documentation.
The @uses tag automatically creates a virtual @used-by tag in the other documentation that links to the documentation containing the @uses tag. In other words, it is exactly like @see, except a return link is added automatically.

When defining a reference to another :term:`Structural Elements` you can either
provide a :term:`Type` or refer to a specific element by appending a double colon and providing the name of that element (also called the :term:`FQSEN`).

The @uses tag SHOULD have a description appended to indicate the type of
reference defined by this occurrence.

Effects in phpDocumentor
------------------------

:term:`Structural Elements`, or inline text in a long description, tagged with
the @uses tag will show a link in their description. If a description is
provided with the tag then this will be used as link text instead of the URL
itself.

Examples
--------

.. code-block:: php
   :linenos:

    /**
     * @uses MyClass                   sets a temporary variable 
     * @uses MyClass::$items           for the property whose items are counted
     * @uses MyClass::setItems()       to set the items for this collection.
     *
     * @return integer Indicates the number of items.
     */
    function count()
    {
        <...>
    }

