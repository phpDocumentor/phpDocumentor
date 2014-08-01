@subpackage
===========

.. important::

   This tag is considered deprecated and may be removed in a future version of
   phpDocumentor. It is recommended to use the :doc:`package` tag's ability to
   provide multiple levels.

The @subpackage tag is used to categorize :term:`Structural Elements` into
logical subdivisions.

Syntax
------

    @subpackage [name]

Description
-----------

The @subpackage tag can be used as a counterpart or supplement to Namespaces.
Namespaces provide a functional subdivision of :term:`Structural Elements` where
the @subpackage tag can provide a *logical* subdivision in which way the
elements can be grouped with a different hierarchy.

If, across the board, both logical and functional subdivisions are equal it is
NOT RECOMMENDED to use the @subpackage tag to prevent maintenance overhead.

The @subpackage tag MUST only be used in a select set of DocBlocks, as is
described in the documentation for the :doc:`package` tag.

The @subpackage tag MUST only be used in a select set of DocBlocks, as is
described in the documentation for the :doc:`package` tag. It MUST also
accompany a :doc:`package` tag and may occur only once per DocBlock.

This tag is considered superseded by the support for multiple levels in the
:doc:`package` tag and is as such considered deprecated.

Effects in phpDocumentor
------------------------

:term:`Structural Elements` tagged with the @subpackage tag are grouped and
organized in their own sidebar section.

Examples
--------

.. code-block:: php
   :linenos:

    /**
     * @package PSR
     * @subpackage Documentation\API
     */
