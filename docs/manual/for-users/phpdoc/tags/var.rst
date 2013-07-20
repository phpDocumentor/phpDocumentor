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

With the @var tag it is possible to document the type and function of a 
class property. When provided it MUST contain a :term:`Type` to indicate what 
is expected; the description on the other hand is OPTIONAL yet RECOMMENDED in 
case of complicated structures, such as associative arrays.

The @var tag MAY have a multi-line description and does not need explicit
delimiting.

It is RECOMMENDED when documenting to use this tag with every property.

This tag MUST NOT occur more than once per property in a :term:`PHPDoc` and is
limited to :term:`Structural Elements` of type property.


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
