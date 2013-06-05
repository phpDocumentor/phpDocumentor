@method
=======

The @method allows a class to know which 'magic' methods are callable.

Syntax
------

    @method [return type] [name]([[type] [parameter]<, ...>]) [<description>]

Description
-----------

The @method tag is used in situation where a class contains the ``__call()``
magic method and defines some definite uses.

An example of this is a child class whose parent has a __call() to have dynamic
getters or setters for predefined properties. The child knows which getters and
setters need to be present but relies on the parent class to use the __call()
method to provide it. In this situation, the child class would have a @method
tag for each magic setter or getter method.

The @method tag allows the author to communicate the type of the arguments and
return value by including those types in the signature.

When the intended method does not have a return value then the return type MAY
be omitted; in which case 'void' is implied.

@method tags MUST NOT be used in a :term:`PHPDoc` that is not associated with
a *class* or *interface*.

Effects in phpDocumentor
------------------------

:term:`Structural Elements` of type *class* or *interface* tagged with the
@method tag will show an extra method in their method listing matching the
data provided with this tag.

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
     * @method void setInteger(integer $integer)
     * @method setString(integer $integer)
     */
    class Child extends Parent
    {
        <...>
    }

