@stable-parameter-names
=======================

The ``@stable-parameter-names`` tag is used to tell callers that they MAY pass
arguments by name, because parameter names are covered by the backward
compatibility promise.

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

    @stable-parameter-names [<description>]

Description
-----------

With `named arguments`_, parameter names are part of the way a function can be
called. This tag tells callers how they should pass arguments. It also tells
maintainers which parts of the signature are covered by the backward
compatibility promise.

Callers MAY pass arguments by name or by position.

For maintainers, parameter names are stable, and so is their order. Renaming a
parameter is a backward compatibility break.

When the tag is used on a class, interface, trait or enum, it applies to all
the methods of that element. A method that overrides or implements a tagged
method has the same contract, even when its PHPDoc does not repeat the tag.

This tag cannot be combined with :doc:`optional-arguments-by-name`,
:doc:`arguments-by-name` or ``@no-named-arguments``. The
``@no-named-arguments`` tag tells callers not to pass arguments by name,
because parameter names can change.

There is nothing to report at call sites. Tools that compare signatures, for
example to check inheritance or backward compatibility, MAY report a renamed
parameter as a break.

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
     * @stable-parameter-names
     */
    interface MailerInterface
    {
        public function send(string $to, string $subject, string $body = ''): void;
    }

    $mailer->send('alice@example.com', 'Hello');              // OK
    $mailer->send(to: 'alice@example.com', subject: 'Hello'); // OK

.. _named arguments: https://www.php.net/functions.arguments#functions.named-arguments
