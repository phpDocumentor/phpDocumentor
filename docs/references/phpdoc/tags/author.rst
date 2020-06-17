@author
=======

The ``@author`` tag is used to document the author of *Structural Elements*.

Syntax
------

.. code-block::

    @author [name] [<email address>]

Description
-----------

The ``@author`` tag can be used to indicate who has created a *Structural Element*
or has made significant modifications to it. This tag MAY also contain an
e-mail address. If an e-mail address is provided it MUST follow
the author's name and be contained in chevrons, or angle brackets, and MUST
adhere to the syntax defined in section 3.4.1 of `RFC 5322`_ or `RFC 2822`_.

Effects in phpDocumentor
------------------------

*Structural Elements* tagged with the ``@author`` tag will show an *Author*
header in their description containing the contents of this tag.

If an e-mail address is provided in the tag then the name of the *Author* will
link to the e-mail address instead of displaying it.

Examples
--------

.. code-block:: php
   :linenos:

    /**
     * @author My Name
     * @author My Name <my.name@example.com>
     */


.. _RFC 2822:      https://www.ietf.org/rfc/rfc2822.txt
.. _RFC 5322:      https://www.ietf.org/rfc/rfc5322.txt
