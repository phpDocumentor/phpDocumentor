@deprecated
===========

The @deprecated tag is used to indicate which :term:`Structural elements` are
deprecated and are to be removed in a future version.

Syntax
------

    @deprecated [<version>] [<description>]

Description
-----------

The @deprecated tag declares that the associated :term:`Structural elements` will
be removed in a future version as it has become obsolete or its usage is otherwise
not recommended.

This tag MAY also contain a version number up till which it is guaranteed to be
included in the software. Starting with the given version, the function will be
removed or may be removed without further notice. If specified, the version number
MUST follow the same rules as those by the :term:`@version` tag's vector.

It is RECOMMENDED (but not required) to provide an additional description stating
why the associated element is deprecated.
If it is superceded by another method it is RECOMMENDED to add a @see tag in the
same :term:`PHPDoc` pointing to the new element.

Effects in phpDocumentor
------------------------

:term:`Structural Elements` tagged with the @deprecated tag will be listed in the
*Deprecated elements* report and their name will be shown as strike through.

Examples
--------

.. code-block:: php
   :linenos:

    /**
     * @deprecated
     * @deprecated 1.0.0
     * @deprecated No longer used by internal code and not recommended.
     * @deprecated 1.0.0 No longer used by internal code and not recommended.
     */
    function count()
    {
        <...>
    }