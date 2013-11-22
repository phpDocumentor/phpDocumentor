@category
==========

.. important::

   This tag is considered deprecated and may be removed in a future version of
   phpDocumentor. It is recommended to use the :doc:`package` tag's ability to
   provide multiple levels.

The @category tag is used to organize groups of packages together.

Syntax
------

    @category [description]

Description
-----------

The @category tag was meant in the original de-facto Standard to group several
:term:`Structural Elements` their :doc:`package` tags into one category. These
categories could then be used to aid in the generation of API documentation.

This was necessary since the @package tag, as defined in the original Standard,
did not contain more then one hierarchy level; since this has changed this tag
SHOULD NOT be used.

Please see the documentation for :doc:`package` for details of its usage.

This tag MUST NOT occur more than once in a :term:`DocBlock`.

Effects in phpDocumentor
------------------------

The @category tag has no significant effect in phpDocumentor. It will be shown
with the description information of associated :term:`Structural Elements` and
is inherited by classes and interfaces.

Examples
--------

.. code-block:: php
   :linenos:

    /**
     * Page-Level DocBlock
     *
     * @category MyCategory
     * @package  MyPackage
     */

