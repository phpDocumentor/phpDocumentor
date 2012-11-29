@since
======

.. important::

   The effects of this tag are not yet fully implemented in PhpDocumentor2.

The @since tag indicates at with which version did the associated
:term:`Structural Elements` became available.

Syntax
------

    @since [version] [<description>]

Description
-----------

The @since tag can be used to indicate since which version specific
:term:`Structural Elements` have become available.

This information can be used to generate a set of API Documentation where the
consumer is informed which application version is necessary for a specific
element.

The version MUST follow the same rules as the :term:`@version` tag's vector and
MAY have a description to provide additional information.

This tag can occur multiple times within a :term:`PHPDoc`. In that case, each
occurrence is treated as an entry to a change log. It is RECOMMENDED that you
also provide a description to each such tag.

Effects in phpDocumentor
------------------------

phpDocumentor shows the version information with the documented element.

Examples
--------

.. code-block:: php
   :linenos:

    /**
     * @since 1.0.1 First time this was introduced.
     *
     * @return integer Indicates the number of items.
     */
    function count()
    {
        <...>
    }

    /**
     * @since 1.0.2 Added the $b argument.
     * @since 1.0.1 Added the $a argument.
     * @since 1.0.0
     *
     * @return void
     */
    function dump($a, $b)
    {
        <...>
    }
