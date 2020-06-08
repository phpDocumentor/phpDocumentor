@since
======

The ``@since`` tag is used to denote _when_ an element was introduced or modified.

Syntax
------

.. code-block::

    @since [<version>] [<description>]

Description
-----------

The ``@since`` tag can be used to document the "version" of the introduction
or modification of a *Structural Element*.

It is RECOMMENDED that the version matches a semantic version number (x.x.x)
and it MAY have a description to provide additional information.

This information can be used to generate a set of API Documentation where the
consumer is informed which application version is necessary for a specific
element.

This tag can occur multiple times within a PHPDoc. In that case, each
occurrence is treated as an entry to a change log. It is RECOMMENDED that you
also provide a description to each such tag.

The ``@since`` tag SHOULD NOT be used to show the current version of an element,
the :doc:`version` tag MAY be used for that purpose.

Effects in phpDocumentor
------------------------

.. important::

   The effects of this tag are not yet fully implemented in phpDocumentor 3.

PhpDocumentor shows the version information with the documented element.

Examples
--------

.. code-block:: php
   :linenos:

    /**
     * @version 1.5.0
     *
     * @since 1.0.1 First time this was introduced.
     *
     * @return int Indicates the number of items.
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
