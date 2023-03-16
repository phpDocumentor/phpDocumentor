@see
====

The ``@see`` tag indicates a reference from the associated
*Structural Element(s)* to a website or other *Structural Element(s)*.

Syntax
------

.. code-block::

    @see [URI | FQSEN] [<description>]

or inline

.. code-block::

   {@see [URI | FQSEN] [<description>]}

Description
-----------

The ``@see`` tag can be used to define a reference to other *Structural Elements*
or to a URI.

When defining a reference to other *Structural Elements*, you can refer to
a specific element by appending a double colon and providing the name of that
element (also called the "Fully Qualified Structural Element Name" or _FQSEN_).

A URI MUST be complete and well-formed as specified in `RFC 2396`_.

The ``@see`` tag SHOULD have a description to provide additional information
regarding the relationship between the element and its target.

The ``@see`` tag cannot refer to a namespace element.

Effects in phpDocumentor
------------------------

*Structural Elements*, or inline text in a long description, tagged with
the ``@see`` tag will show a link in their description.

If a description is provided with the inline version of tag then this will be
used as link text instead of the URL itself.

In addition, phpDocumentor provides the ``doc://`` virtual scheme. When you
provide this scheme, phpDocumentor will interpret that as a reference to
:doc:`hand-written documentation<../../../hand-written-docs/index>`.

Examples
--------

Normal tag:

.. code-block:: php
   :linenos:

    /**
     * @see number_of()                 Alias.
     * @see MyClass::$items             For the property whose items are counted.
     * @see MyClass::setItems()         To set the items for this collection.
     * @see https://example.com/my/bar  Documentation of Foo.
     * @see doc://getting-started/index Getting started document.
     *
     * @return int Indicates the number of items.
     */
    function count()
    {
        <...>
    }

Inline tag:

.. code-block:: php
   :linenos:

    /**
     * @return int Indicates the number of {@see \Vendor\Package\ClassName}
     *             items.
     */
    function count()
    {
        <...>
    }

.. _RFC 2396:      https://www.ietf.org/rfc/rfc2396.txt
