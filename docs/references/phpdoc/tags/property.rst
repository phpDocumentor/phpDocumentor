@property, @property-read, @property-write
===========================================

The ``@property`` tag is used to declare which "magic" `properties`_ are supported.

Syntax
------

.. code-block::

    @property[<-read|-write>] [Type] [name] [<description>]

Description
-----------

The ``@property`` tag is used when a ``class`` or ``trait`` implements the
`__get() <https://www.php.net/language.oop5.overloading#object.get>`_ and/or
`__set() <https://www.php.net/language.oop5.overloading#object.set>`_ "magic"
methods to resolve non-literal `properties`_ at run-time.

The ``@property-read`` and ``@property-write`` variants MAY be used to indicate "magic"
properties that can only be read or written.

For example, the ``@property-read`` tag could be used when a class contains
a `__get() <https://www.php.net/language.oop5.overloading#object.get>` magic
method which allows for specific names, while those names are not covered in the
`__set() <https://www.php.net/language.oop5.overloading#object.set>`_ magic method.

============================ =====================
Property supported via       Tag to use
============================ =====================
``__get()`` and ``__set()``  ``@property``
``__get()`` only             ``@property-read``
``__set()`` only             ``@property-write``
============================ =====================

The ``@property``, ``@property-read`` and ``@property-write`` tags can ONLY be used
in a PHPDoc that is associated with a *class* or *trait*.

Effects in phpDocumentor
------------------------

*Structural Elements* of type *class* or *trait* tagged with the
``@property``, ``@property-read`` or ``@property-write`` tag will show an extra
property in their property listing matching the data provided with this tag.


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

        public function __set()
        {
            <...>
        }
    }

    /**
     * @property       string $myProperty
     * @property-read  string $myReadOnlyProperty
     * @property-write string $myWriteOnlyProperty
     */
    class Child extends Parent
    {
        <...>
    }


In the following concrete example, a class ``User`` implements the magic ``__get()`` method,
in order to implement a "magic", read-only ``$full_name`` property:

.. code-block:: php
   :linenos:

    /**
     * @property-read string $full_name
     */
    class User
    {
        /**
         * @var string
         */
        public $first_name;

        /**
         * @var string
         */
        public $last_name;

        public function __get($name)
        {
            if ($name === "full_name") {
                return "{$this->first_name} {$this->last_name}";
            }
        }
    }


.. _properties : https://www.php.net/language.oop5.properties
