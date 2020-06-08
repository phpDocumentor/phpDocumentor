@ignore
=======

The ``@ignore`` tag is used to tell phpDocumentor that *Structural Elements* are not
to be processed by phpDocumentor.

Syntax
------

.. code-block::

    @ignore [<description>]

Description
-----------

The ``@ignore`` tag tells phpDocumentor that the *Structural Elements* associated
with the tag should not to be processed. An typical use-case would be to prevent
showing duplicate documentation for conditional constants.

It is RECOMMENDED, but not required, to provide an additional description stating
why the associated element is to be ignored.

Effects in phpDocumentor
------------------------

*Structural Elements* tagged with the ``@ignore`` tag will be not be processed.

Examples
--------

.. code-block:: php
   :linenos:

    if ($ostest) {
        /**
         * This define will either be 'Unix' or 'Windows'
         */
        define("RUNTIME_OS","Unix");
    } else {
        /**
         * @ignore
         */
        define("RUNTIME_OS","Windows");
    }
