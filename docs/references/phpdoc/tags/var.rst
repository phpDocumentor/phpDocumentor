@var
====

You may use the ``@var`` tag to document the :doc:`Type <../types>` of
the following *Structural Elements*:

* Constants, both class and global scope
* Properties
* Variables, both global and local scope

Syntax
------

.. code-block::

    @var [Type] [element name] [<description>]

Description
-----------

The ``@var`` tag defines which :doc:`Type <../types>` of data is represented
by the value of a constant_, property_ or variable_.

Each constant or property definition or variable where the :doc:`Type <../types>`
is ambiguous or unknown SHOULD be preceded by a DocBlock containing the ``@var``
tag. Any other variable MAY be preceded with a DocBlock containing the ``@var`` tag.

The ``@var`` tag MUST contain the name of the element it documents. An exception
to this is when a declaration only refers to a single property or constant.
In that case, the name of the property or constant MAY be omitted.

The name is used when compound statements are used to define a series of constants
or properties. Such a compound statement can only have one DocBlock while several
items are represented.

Effects in phpDocumentor
------------------------

Constants, properties and global variables, that are tagged with the
``@var`` tag, will have their *Type* displayed in their signature.

If the *Type* is a class that is documented by phpDocumentor,
then a link to that class' documentation is provided.


Examples
--------

.. code-block:: php
   :linenos:

    /** @var int $int This is a counter. */
    $int = 0;

    // There should be no docblock here.
    $int++;

    class Foo
    {
        /**
         * Full docblock with a summary.
         *
         * @var int
         */
        const INDENT = 4;

        /** @var string|null Short docblock, should contain a description. */
        protected $description = null;

        public function setDescription($description)
        {
            // There should be no docblock here.
            $this->description = $description;
        }
    }

Another example is to document the variable in a foreach explicitly; many IDEs
use this information to help you with auto-completion:

.. code-block:: php
   :linenos:

    /** @var \Sqlite3 $sqlite */
    foreach ($connections as $sqlite) {
        // There should be no docblock here.
        $sqlite->open('/my/database/path');
        <...>
    }


Even compound class constant and property statements may be documented:

.. code-block:: php
   :linenos:

    class Foo
    {
        /**
         * @var string $name        Should contain a description
         * @var string $description Should contain a description
         */
        protected $name,
            $description = 'Default description';
    }

.. _constant: https://www.php.net/language.constants
.. _property: https://www.php.net/language.oop5.properties
.. _variable: https://www.php.net/language.variables
