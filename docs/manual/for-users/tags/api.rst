@api
====

The @api tag is used to declare :term:`Structural Elements` as being suitable for
consumption by third parties.

Syntax
------

    @api

Description
-----------

The @api tag represents those :term:`Structural Elements` with a public visibility
which are intended to be the public API components for a library or framework.
Other :term:`Structural Elements` with a public visibility serve to support the
internal structure and are not recommended to be used by the consumer.

The exact meaning of :term:`Structural Elements` tagged with @api MAY differ per
project. It is however RECOMMENDED that all tagged :term:`Structural Elements` SHOULD
NOT change after publication unless the new version is tagged as breaking
Backwards Compatibility.

Effects in phpDocumentor
------------------------

:term:`Structural Elements` tagged with the @api tag will be shown in a separate
sidebar section and the individual entry of will be marked as being an API element.

    Not all templates might show the API sidebar section; it is recommended to
    check this before using a specific template.

Examples
--------

.. code-block:: php
   :linenos:

    /**
     * This method will not change until a major release.
     *
     * @api
     *
     * @return void
     */
     function showVersion()
     {
        <...>
     }
