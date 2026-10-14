@optional-arguments-by-name
===========================

The ``@optional-arguments-by-name`` tag is used to tell callers that they MUST
pass the arguments of optional parameters by name.

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

    @optional-arguments-by-name [<description>]

Description
-----------

With `named arguments`_, parameter names are part of the way a function can be
called. This tag tells callers how they should pass arguments. It also tells
maintainers which parts of the signature are covered by the backward
compatibility promise.

Callers MUST pass the arguments of optional parameters by name. A parameter is
optional when it has a default value. Callers SHOULD pass the arguments of
required parameters by position, because the names of required parameters are
not part of the contract. Variadic parameters are not affected.

For maintainers, optional parameters are never renamed, but their order can
change. Required parameters keep their order, but their names can change. This
means you can add, reorder or remove optional parameters (with the usual
deprecation) without breaking callers.

Making a required parameter optional breaks callers. They pass required
arguments by position and optional arguments by name, so their existing calls
would not follow the tag anymore.

This tag is useful for functions that have many optional parameters, for
example the constructor of a value object.

When the tag is used on a class, interface, trait or enum, it applies to the
public and protected methods of that element. In a trait, it applies to private
methods too, because the classes that use the trait can call them.

A method that implements or overrides a tagged method has the same contract,
even when its PHPDoc does not repeat the tag. When you write such a method, you
MUST keep the same parameter names. Code that calls your method through the
interface or the parent class can pass arguments by name. This does not apply
to constructors.

This tag cannot be combined with :doc:`arguments-by-name`,
:doc:`stable-parameter-names` or ``@no-named-arguments``. The
``@no-named-arguments`` tag tells callers not to pass arguments by name,
because parameter names can change.

Static analysis tools MAY report an argument that is passed by position to an
optional parameter.

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

    final class Configuration
    {
        /**
         * @optional-arguments-by-name
         */
        public function __construct(
            string $dialect,
            array $groups = [],
            bool $allowExtraAttributes = true,
        ) {
            <...>
        }
    }

    new Configuration('openapi-3.1', groups: ['read']); // OK
    new Configuration('openapi-3.1', ['read']);         // $groups must be passed by name
    new Configuration(dialect: 'openapi-3.1');          // works, but $dialect can be renamed

.. _named arguments: https://www.php.net/functions.arguments#functions.named-arguments
