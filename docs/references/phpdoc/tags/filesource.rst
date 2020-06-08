@filesource
===========

The ``@filesource`` tag is used to tell phpDocumentor to include the source of the
current file in the parsing results.

Syntax
------

.. code-block::

    @filesource

Description
-----------

The ``@filesource`` tag tells phpDocumentor to include the source of the current file
in the output.

As this only applies to the source code of the entire file, this tag MUST be used
in the file-level PHPDoc. Any other location will be ignored.

Effects in phpDocumentor
------------------------

When this tag is included, phpDocumentor will compress the file contents and encode it
using Base64, so that it can be handled by the transformer.

Any template that is able to show the source code can then read the ``source`` sub-element
in the associated ``file`` element of the Abstract Syntax Tree.

Examples
--------

.. code-block:: php
   :linenos:

    <?php
    /**
     * @filesource
     */
