@version
========

The ``@version`` tag is used to denote the current version of *Structural Elements*.

Syntax
------

.. code-block::

    @version [<Semantic Version>] [<description>]

Description
-----------

The ``@version`` tag can be used to indicate the current "version" of
*Structural Elements*.

This information can be used to generate a set of API Documentation where the
consumer is informed about elements at a particular version.

It is RECOMMENDED that the version number matches a semantic version number as
described in the `Semantic Versioning Standard version 2.0`_.

Version vectors from Version Control Systems are also supported, though they
MUST follow the form:

    name-of-vcs: $vector$

A description MAY be provided, for the purpose of communicating any additional
version-specific information.

The ``@version`` tag MAY NOT be used to show the last modified or introduction
version of an element, the :doc:`since` tag SHOULD be used for that purpose.


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
     *          (custom Git-based replacement keyword)
     * @version @package_version@
     *          (this PEAR replacement keyword expands upon package installation)
     * @version $Id$
     *          (this CVS keyword expands to show the CVS file revision number)
     */
    class NeoCounter
    {
        <...>
    }


.. _Semantic Versioning Standard version 2.0  https://semver.org/
