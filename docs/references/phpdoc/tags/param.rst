@param
======

The ``@param`` tag is used to document a single `argument of a function or method`_.

Syntax
------

.. code-block::

    @param [Type] [name] [<description>]

Description
-----------

With the ``@param`` tag it is possible to document the :doc:`Type <../types>`
and function of a single `argument of a function or method`_. When provided it
MUST contain a :doc:`Type <../types>` to indicate what is expected.
The description is OPTIONAL yet RECOMMENDED, for instance, in case of
complicated structures, such as associative arrays.

The ``@param`` tag MAY have a multi-line description and does not need explicit
delimiting.

It is RECOMMENDED when documenting to use this tag with every function and
method.

This tag MUST NOT occur more than once per argument in a PHPDoc and is
limited to *Structural Elements* of type method or function.

Effects in phpDocumentor
------------------------

*Structural Elements* of type method or function, that are tagged with the
``@param`` tag, will have additional information in the section regarding Parameters.

If the parameter *Type* is a class that is documented by phpDocumentor,
then a link to that class' documentation is provided.

.. note::

   phpDocumentor supports ``@param`` tags which omit the name, this is
   NOT RECOMMENDED but provided for compatibility with existing projects.

.. note::

   phpDocumentor will not verify whether a documented type is in line with
   a declared type.
   Such verification, if desired, can be executed by static analyzer tools
   like `PHPStan`_ or `PHP_CodeSniffer`_.

Examples
--------

.. code-block:: php
   :linenos:

    /**
     * Counts the number of items in the provided array.
     *
     * @param mixed[] $items     Array structure to count the elements of.
     * @param bool    $recursive Optional. Whether or not to recursively
     *                           count elements in nested arrays.
     *                           Defaults to `false`.
     *
     * @return int Returns the number of elements.
     */
    function count(array $items, bool $recursive = false)
    {
        <...>
    }


.. _argument of a function or method: https://www.php.net/functions.arguments
.. _PHPStan:                          https://phpstan.org/
.. _PHP_CodeSniffer:                  https://github.com/squizlabs/php_codesniffer/
