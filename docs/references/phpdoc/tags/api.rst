@api
====

The ``@api`` tag is used to highlight *Structural Elements* as being part of the
primary public API of a package.

Syntax
------

.. code-block::

    @api

Description
-----------

The ``@api`` tag may be applied to public *Structural Elements* to highlight
them in generated documentation, pointing the consumer to the primary public
API components of a library or framework.

When the ``@api`` tag is used, other *Structural Elements* with a public
visibility serve to support the internal structure and are not recommended
to be used by the consumer.

The exact meaning of *Structural Elements* tagged with ``@api`` MAY differ per
project. It is however RECOMMENDED that all ``@api`` tagged *Structural Elements*
SHOULD NOT change after publication unless the new version is tagged as breaking
Backwards Compatibility.

See also the :doc:`internal` tag, which can be used to hide internal
*Structural Elements* from generated documentation.

Effects in phpDocumentor
------------------------

*Structural Elements* tagged with the ``@api`` tag will be shown in a separate
sidebar section and the individual entry will be marked as being an API element.

    Not all templates may show the API sidebar section. If your project uses the
    ``@api`` tag, it is recommended to verify your preferred template_ supports
    the API sidebar before generating the documentation.

Examples
--------

.. code-block:: php
   :linenos:

    class UserService
    {
        /**
         * This method is public-API.
         *
         * This method will not change until a major release.
         *
         * @api
         */
        public function getUser()
        {
            <...>
        }

        /**
         * This method is "package scope", not public-API.
         */
        public function callMefromAnotherClass()
        {
            <...>
        }
    }


.. _template:      https://phpdoc.org/templates
