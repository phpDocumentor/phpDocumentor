@var
====

You may use the @var tag to document the "Type" of properties, sometimes called class variables.

Syntax
------

    @var ["Type"] [$element_name] [<description>]

Description
-----------

The @var tag defines which type of data is represented by the value of a property_.

The @var tag MUST contain the name of the element it documents. An exception to this is when property declarations only
refer to a single property. In this case the name of the property MAY be omitted.

This is used when compound statements are used to define a series of properties. Such a compound statement can only have
one DocBlock while several items are represented.

Examples
--------

.. code-block:: php

    class Foo
    {
      /** @var string|null Should contain a description */
      protected $description = null;
    }

Even compound statements may be documented:

.. code-block:: php

    class Foo
    {
      /**
       * @var string $name        Should contain a description
       * @var string $description Should contain a description
       */
      protected $name, $description;
    }

.. _property: http://www.php.net/manual/en/language.oop5.properties.php
