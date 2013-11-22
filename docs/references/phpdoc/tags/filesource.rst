@filesource
===========

The @filesource tag is used to tell phpDocumentor to include the source of the
current file in the parsing results.

Syntax
------

    @filesource

Description
-----------

The @filesource tag tells phpDocumentor to include the current file in the parsing
output. As this only applies to the source code of the entire file MUST this
tag be used in the file-level :term:`PHPDoc`. Any other location will be ignored.

When this tag is included will phpDocumentor compress the file contents and encode them
using Base64 so that it can be handled by the transformer. Any template that
is able to show the source code can then read the ``source`` sub-element in the
associated ``file`` element in the :term:`Abstract Syntax Tree`.

Examples
--------

.. code-block:: php
   :linenos:

    /**
     * @filesource
     */
