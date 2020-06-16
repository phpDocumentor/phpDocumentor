@deprecated
===========

The ``@deprecated`` tag is used to indicate which *Structural Elements* are
deprecated and are to be removed in a future version.

Syntax
------

.. code-block::

    @deprecated [<Semantic Version>] [<description>]

Description
-----------

The ``@deprecated`` tag declares that the associated Structural Element(s) will
be removed in a future version as it has become obsolete or its usage is otherwise
not recommended, effective from the "Semantic Version" if provided.

If specified, the version number MUST follow the same rules as a version number used
by the :doc:`version` tag's vector.

This tag MAY provide an additional description stating why the associated
element is deprecated.

If the associated element is superseded by another, it is RECOMMENDED to add a
:doc:`see` tag in the same PHPDoc pointing to the new element.

Effects in phpDocumentor
------------------------

Structural Elements tagged with the ``@deprecated`` tag will be listed in the
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
