@unsafe
===========

The ``@unsafe`` tag is used to indicate *Structural Elements* whose
use poses an unusually high risk and should be used with caution.

Syntax
------

.. code-block::

    @unsafe [<description>]

Description
-----------

The ``@unsafe`` tag declares that the associated Structural Element(s) pose an
unusually high risk and should be used with caution.

This tag MAY provide an additional description stating why the associated
element is unsafe.

Effects in phpDocumentor
------------------------

Structural Elements tagged with the ``@unsafe`` tag will be listed in the
*Unsafe elements* report.

Examples
--------

.. code-block:: php
   :linenos:

    /**
     * @unsafe This setter can cause race conditions and should be used with caution.
     */
    function setFoo()
    {
        <...>
    }
