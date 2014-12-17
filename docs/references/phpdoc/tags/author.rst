@author
=======

The @author tag is used to document the author of :term:`Structural Elements`.

Syntax
------

    @author [name] [<email address>]

Description
-----------

The @author tag can be used to indicate who has created :term:`Structural Elements`
or has made significant modifications to them. This tag MAY also contain an
e-mail address. If an e-mail address is provided it MUST follow
the author's name and be contained in chevrons, or angle brackets, and MUST
adhere to the syntax defined in section 3.4.1 of
`RFC5322 <http://www.ietf.org/rfc/rfc5322.txt>`_.

Effects in phpDocumentor
------------------------

:term:`Structural Elements` tagged with the @author tag will show an *Author*
header in their description containing the contents of this tag.

If an e-mail address is provided in the tag then the *Author* will link to the
mail address instead of displaying it.

Examples
--------

.. code-block:: php
   :linenos:

    /**
     * @author My Name
     * @author My Name <my.name@example.com>
     */
