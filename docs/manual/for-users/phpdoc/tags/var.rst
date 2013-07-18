@var
====

The @var tag is used to document a class property.

Syntax
------

   @var [:term:`Type`] [<description>]

   @var [:term:`Type`] [name] [<description>]


   [<summary>]

   @var [:term:`Type`] [<description>]

   [<summary>]

   @var [:term:`Type`] [name] [<description>]

Description
-----------




Effects in phpDocumentor
------------------------

.. NOTE::

   The name of the property will be ommitted from the documentation if you specify it.
   The name is derived from the variable itself.

.. NOTE::

   Specifying the summary is optional if there is a description.

Examples
--------

.. code-block:: php
   :linenos:
   
   class DemoVar
   {
      /**
       * Summary
       *
       * @var object Description
       */
      protected $varWithDescriptions;
      
      /**
       * @var \DemoVar $instance The class instance.
       */
      protected static $instance;
      
      /**
       * Summary for varWithWrongType
       *
       * @var boolean The varWithWrongType. Boolean will be put in the type.
       */
      protected $varWithWrongType = array();
   }
