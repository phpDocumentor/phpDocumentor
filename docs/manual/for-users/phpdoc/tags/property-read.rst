@property-read
==============

The @property-read tag allows a class to know which 'magic' properties are
present that are read-only.

Syntax
------

    @property-read [:term:`Type`] [name] [<description>]

Description
-----------

The @property-read tag is used in the situation where a class contains the
``__get()`` magic method and allows for specific names that are not covered in
a ``__set()`` magic method.

An example of this is a child class whose parent has a __get(). The child knows
which properties need to be present but relies on the parent class to use the
__get() method to provide it.
In this situation, the child class would have a @property-read tag for each
magic property.

@property-read tags MUST NOT be used in a :term:`PHPDoc` that is not associated
with a *class* or *interface*.

Effects in phpDocumentor
------------------------

:term:`Structural Elements` of type *class* or *interface* tagged with the
@property-read tag will show an extra property in their property listing
matching the data provided with this tag.

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
     * @property-read string $myProperty
     */
    class Child extends Parent
    {
        <...>
    }

