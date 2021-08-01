@method
=======

The ``@method`` tag allows a class to know which 'magic' methods are callable.

Syntax
------

.. code-block::

    @method [[static] return type] [name]([[type] [parameter]<, ...>]) [<description>]

Description
-----------

The ``@method`` tag is used in situations where a class contains the
`\__call() <https://www.php.net/language.oop5.overloading#object.call>`_ or
`\__callStatic() <https://www.php.net/language.oop5.overloading#object.callstatic>`_
magic method and defines some definite uses.

An example of this is a child class whose parent has a ``__call()`` method defined
to have dynamic getters or setters for predefined properties. The child knows
which getters and setters need to be present, but relies on the parent class to
use the `\__call() <https://www.php.net/language.oop5.overloading#object.call>`_
method to provide this functionality. In this situation, the child class would have
a ``@method`` tag for each magic setter or getter method.

The ``@method`` tag allows the author to communicate the :doc:`Type <../types>` of
the arguments and return value by including those types in the signature.

When the intended method does not have a return value then the return
:doc:`type <../types>` MAY be omitted; in which case 'void' is implied.

If the intended method is static, the ``static`` keyword can be placed before
the return type to communicate that.
In that case, a return type MUST be provided, as ``static`` on its own would mean
that the method returns an instance of the child class which the method is called on.

``@method`` tags MUST NOT be used in a PHPDoc unless it is associated with
a *class* or *interface*.

Effects in phpDocumentor
------------------------

*Structural Elements* of type *class* or *interface* tagged with the ``@method``
tag will show an extra method in their method listing matching the data
provided with this tag.

Examples
--------

.. code-block:: php
   :linenos:

    class Parent
    {
        public function __call()
        {
            <...>
        }
    }

    /**
     * @method string getString()
     * @method void setInteger(int $integer)
     * @method setString(int $integer)
     * @method static string staticGetter()
     */
    class Child extends Parent
    {
        <...>
    }
