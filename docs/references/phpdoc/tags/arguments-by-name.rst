@arguments-by-name
==================

The ``@arguments-by-name`` tag is used to tell callers that they MUST pass
all arguments by name.

This tag can only be used on the following *Structural Elements*:

- ``function``
- ``method``
- ``class``
- ``interface``
- ``trait``
- ``enum``

Syntax
------

.. code-block::

    @arguments-by-name [<description>]

Description
-----------

With `named arguments`_, parameter names are part of the way a function can be
called. This tag tells callers how they should pass arguments. It also tells
maintainers which parts of the signature are covered by the backward
compatibility promise.

Callers MUST pass every argument by name. Variadic parameters are not affected.

For maintainers, parameter names are stable but their order is not. This means
you can reorder parameters without breaking callers.

When the tag is used on a class, interface, trait or enum, it applies to all
the methods of that element. A method that overrides or implements a tagged
method has the same contract, even when its PHPDoc does not repeat the tag.

This tag cannot be combined with :doc:`optional-arguments-by-name`,
:doc:`stable-parameter-names` or ``@no-named-arguments``. The
``@no-named-arguments`` tag tells callers not to pass arguments by name,
because parameter names can change.

Static analysis tools MAY report every argument that is passed by position.

The description is OPTIONAL and MAY explain the contract to the reader.

Effects in phpDocumentor
------------------------

phpDocumentor has no special support for this tag. Like other tags that
phpDocumentor does not know, the tag is listed with its description in the
*Tags* section of the element. It is not copied to the methods of a tagged
class, nor to the methods that override a tagged method.

Examples
--------

.. code-block:: php
   :linenos:

    /**
     * @arguments-by-name
     */
    function createUser(string $name, string $email, bool $admin = false)
    {
        <...>
    }

    createUser(name: 'Alice', email: 'alice@example.com'); // OK
    createUser(email: 'alice@example.com', name: 'Alice'); // OK
    createUser('Alice', 'alice@example.com');              // arguments must be passed by name

.. _named arguments: https://www.php.net/functions.arguments#functions.named-arguments
