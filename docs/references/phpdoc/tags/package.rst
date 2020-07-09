@package
========

The ``@package`` tag is used to categorize *Structural Elements* into logical
subdivisions.

Syntax
------

.. code-block::

    @package [level 1]\\[level 2]\\[etc.]

Description
-----------

The ``@package`` tag can be used as a counterpart or supplement to Namespaces_.
Namespaces provide a functional subdivision of *Structural Elements* where
the ``@package`` tag can provide a *logical* subdivision in which way the elements
can be grouped with a different hierarchy.

If, across the board, both logical and functional subdivisions are equal is it
NOT RECOMMENDED to use the ``@package`` tag to prevent maintenance overhead.

Each level in the logical hierarchy MUST be separated with a backslash (``\``) to
be familiar to Namespaces. A hierarchy MAY be of endless depth but it is
RECOMMENDED to keep the depth at less or equal than six levels.

Please note that the ``@package`` tag applies to different *Structural Elements*
depending where it is defined.

1. If the *package* is defined in the *file-level* DocBlock then it only applies
   to the following elements in the applicable file:

   * global functions
   * global constants
   * global variables
   * requires and includes

2. If the *package* is defined in a *namespace-level* or *class-level* DocBlock
   then the package applies to that namespace, class, trait or interface and their
   contained elements.
   This means that a function which is contained in a namespace with the
   ``@package`` tag assumes that package.

The ``@package`` tag MUST NOT occur more than once in a PHPDoc.

Effects in phpDocumentor
------------------------

*Structural Elements* tagged with the ``@package`` tag are grouped and
organized in their own sidebar section.

.. note::

    Aside from the backslash (``\``), phpDocumentor also allows the
    underscore (``_``) and dot (``.``) as separators for compatibility
    with existing projects. Despite this the backslash is RECOMMENDED
    as separator.

Examples
--------

.. code-block:: php
   :linenos:

    /**
     * @package PSR\Documentation\API
     */

.. _Namespaces: https://www.php.net/language.namespaces
