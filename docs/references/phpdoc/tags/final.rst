@final
======

The `@final` decorator is used to denote that the associated *Structural Element* is final,
are not allowed to extend or override the *Structural Element* in a child element.

This tag can only be used on the following *Structural Elements*:

- ``method``
- ``constant``
- ``class``

Syntax
------

.. code-block::

    @final [description]

Description
-----------

In some situations the language construct ``final`` cannot be used by the implementing
library where the functionality of the library prevents elements from being final. For
example when proxy patterns are applied. In these cases the ``@final`` tag can be used to
indicate that the element should be treated as final.

The optional description is used to provide a more detailed explanation of why the element
is marked as final.

IDE's and other tools can use this information to show an error when such an element is
extended or overridden.

Effects in phpDocumentor
------------------------

*Structural Elements* that are marked as ``@final`` will be displayed
as such in the generated documentation. The same way as the ``final`` keyword.

Example
-------

.. code-block:: php

    /**
     * @final since version X.y.
     */
    class Service
    {
        public function method()
        {
        }
    }

Example of a method marked as final:

.. code-block:: php

    class Service
    {
        /**
         * @final
         */
        public function method()
        {
        }
    }
