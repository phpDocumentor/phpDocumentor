@copyright
==========

The @copyright tag is used to document the copyright information for
:term:`Structural elements`.

Syntax
------

    @copyright [description]

Description
-----------

The @copyright tag defines who holds the copyright over :term:`Structural Elements`.
The copyright indicated with this tag applies to the :term:`Structural Elements`
with which it is associated and all child elements unless otherwise noted.

The format of the description if governed by the coding standard of each
individual project. It is RECOMMENDED to mention the year or years which are
covered by this copyright and the organization involved.

Effects in phpDocumentor
------------------------

:term:`Structural Elements` tagged with the @copyright tag will show a *Copyright*
header in their description containing the contents of this tag.

Examples
--------

.. code-block:: php
   :linenos:

    /**
     * @copyright 1997-2005 The PHP Group
     */
