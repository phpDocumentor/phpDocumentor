@license
========

The ``@license`` tag is used to indicate which license is applicable for the associated
*Structural Elements*.

Syntax
------

.. code-block::

    @license [<url>] [name]

Description
-----------

The ``@license`` tag provides the user with the name and URL of the license that is
applicable to a *Structural Element* and any of its child elements.

It is RECOMMENDED to apply @license tags ONLY to file-level PHPDocs, to prevent
confusion over which license applies to any particular *Structural Element*.

Whenever multiple licenses apply, there MUST be one ``@license`` tag per applicable
license.

Effects in phpDocumentor
------------------------

*Structural Elements* tagged with the ``@license`` tag will show a link to the
given license in case a URL is provided or the name is one of the following
license names:

* GPL, or GNU General Public License, version 2
* GPL, or GNU General Public License, version 3
* LGPL, or GNU (Library|Lesser) General Public License, version 3
* BSD
* FreeBSD
* MIT

phpDocumentor supports several permutations of the names above and it is RECOMMENDED
to try out the form that you desire.

Examples
--------

.. code-block:: php
   :linenos:

    <?php
    /**
     * @license GPL
     *
     * @license https://opensource.org/licenses/gpl-license.php GNU Public License
     */
