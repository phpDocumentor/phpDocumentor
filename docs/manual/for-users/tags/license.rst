@license
========

The @license tag is used to indicate which license is applicable for the associated
:term:`Structural Elements`.

Syntax
------

    @license [<url>] [name]

Description
-----------

The @license tag provides the user with the name and URL of the license that is
applicable to :term:`Structural Elements` and any of their child elements.

It is NOT RECOMMENDED to apply @license tags to any :term:`PHPDoc` other than
file-level PHPDocs as this may cause confusion which license applies at which
time.

Whenever multiple licenses apply MUST there be one @license tag per applicable
license.

Effects in phpDocumentor
------------------------

:term:`Structural Elements` tagged with the @license tag will show a link to the
given license in case an URL is provided or the name contains one of the following
license forms:

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

    /**
     * @license GPL
     * @license http://opensource.org/licenses/gpl-license.php GNU Public License
     */
