@property-write
===============

The @property-write tag allows a class to know which 'magic' properties are
present that are write-only.

Syntax
------

    @property-write [:term:`Type`] [name] [<description>]

Description
-----------

The @property-write tag is used in the situation where a class contains the
``__set()`` magic method and allows for specific names that are not covered in
a ``__get()`` magic method.

An example of this is a child class whose parent has a __set(). The child knows
which properties need to be present but relies on the parent class to use the
__set() method to provide it.
In this situation, the child class would have a @property-write tag for each magic
property.

@property-write tags MUST NOT be used in a :term:`PHPDoc` that is not associated
with a *class* or *interface*.

Effects in phpDocumentor
------------------------

:term:`Structural Elements` of type *class* or *interface* tagged with the
@property-write tag will show an extra property in their property listing
matching the data provided with this tag.

Examples
--------

.. code-block:: php
   :linenos:

    class Parent
    {
        public function __set()
        {
            <...>
        }
    }

    /**
     * @property-write string $myProperty
     */
    class Child extends Parent
    {
        <...>
    }

