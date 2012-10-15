@since
======

The @since tag indicates starting which version the associated
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

The version MUST match a semantic version number (x.x.x) and MAY have a
description to provide additional information.

Effects in phpDocumentor
------------------------

phpDocumentor shows the version information with the documented element.

Examples
--------

.. code-block:: php
   :linenos:

    /**
     * @version 1.0.0
     *
     * @return integer Indicates the number of items.
     */
    function count()
    {
        <...>
    }
