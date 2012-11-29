@version
======

The @version tag indicates the current version of :term:`Structural Elements`.

Syntax
------

    @version [<vector>] [<description>]

Description
-----------

The @version tag can be used to indicate the current version of
:term:`Structural Elements`.

This information can be used to generate a set of API Documentation where the
consumer is informed about elements at a particular version.

The version vector MUST start with a digit. It is RECOMMENDED that it matches a
semantic version number (x.x.x).

Version vectors from Version Control Systems are also recognized, though they
MUST follow the form

    name-of-vcs: $vector$

A description MAY be provided, for the purpose of communicating any additional
version-specific information.

Effects in phpDocumentor
------------------------

phpDocumentor shows the version information with the documented element.

Examples
--------

.. code-block:: php
   :linenos:

    /**
     * @version 1.0.1
     */
    class Counter
    {
        <...>
    }

    /**
     * @version GIT: $Id$ In development. Very unstable.
     */
    class NeoCounter
    {
        <...>
    }