@property
=========

The @property tag allows a class to know which 'magic' properties are present.

Syntax
------

    @property [:term:`Type`] [name] [<description>]

Description
-----------

The @property tag is used in the situation where a class contains the
``__get()`` and ``__set()`` magic methods and allows for specific names.

An example of this is a child class whose parent has a __get(). The child knows
which properties need to be present but relies on the parent class to use the
__get() method to provide it.
In this situation, the child class would have a @property tag for each magic
property.

@property tags MUST NOT be used in a :term:`PHPDoc` that is not associated with
a *class* or *interface*.

Effects in phpDocumentor
------------------------

:term:`Structural Elements` of type *class* or *interface* tagged with the
@property tag will show an extra property in their property listing matching the
data provided with this tag.

Examples
--------

.. code-block:: php
   :linenos:

    class Parent
    {
        public function __get()
        {
            <...>
        }
    }

    /**
     * @property string $myProperty
     */
    class Child extends Parent
    {
        <...>
    }

