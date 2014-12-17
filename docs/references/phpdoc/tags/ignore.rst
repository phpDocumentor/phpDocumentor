@ignore
=======

The @ignore tag is used to tell phpDocumentor that :term:`Structural Elements` are not
to be processed by phpDocumentor.

Syntax
------

    @ignore [<description>]

Description
-----------

The @ignore tag tells phpDocumentor that the :term:`Structural Elements` associated
with the tag are not to be processed. An example of use might be to prevent
duplicate documenting of conditional constants.

It is RECOMMENDED (but not required) to provide an additional description stating
why the associated element is to be ignored.

Effects in phpDocumentor
------------------------

:term:`Structural Elements` tagged with the @ignore tag will be not be processed.

Examples
--------

.. code-block:: php
   :linenos:

    if ($ostest) {
        /**
         * This define will either be 'Unix' or 'Windows'
         */
        define("OS","Unix");
    } else {
        /**
         * @ignore
         */
        define("OS","Windows");
    }
