@var
====

.. important::

   The effects of this tag are not yet fully implemented in PhpDocumentor2.
   
   * The @see tag will not link properly.

The @var tag is used to document a class property.

Syntax
------

   @var [:term:`Type`] [<description>]

   @var [:term:`Type`] [name] [<description>]


   [<header description>]

   @var [:term:`Type`] [<description>]

   [<header description>]

   @var [:term:`Type`] [name] [<description>]

Description
-----------




Effects in phpDocumentor
------------------------

.. NOTE::

   phpDocumentor will try to analyze correct usage and presence of the @var
   tag; as such it will provide error information in the following scenarios:
   
   * A missing docblock for a property: No summary for property [name]
   * A missing summary for a property: No summary for property [name]

.. NOTE::

   The name of the property will be ommitted from the documentation if you specify it.
   The name is derived from the variable itself.

.. NOTE::

   Specifying the header description is optional.

.. NOTE::

   The contents for this chapter are in progress. Why not help us and
   contribute it at
   https://github.com/phpDocumentor/phpDocumentor2/tree/develop/docs/manual

Examples
--------

.. code-block:: php
   :linenos:
   
   class DemoVar
   {
      protected $varNoDocBlock;
      
      /**
       * @var
       */
      protected static $varNoType;
      
      /**
       * @var object
       */
      protected static $varOnlyType;
      
      /**
       * Header description
       *
       * @var object Short description.
       */
      protected $varWithDescriptions;
      
      /**
       * @var \DemoVar $instance The class instance.
       */
      protected static $instance;
      
      /**
       * @var array The class varArray1.
       */
      protected $varArray1 = array();
      
      /**
       * Some more info on varArray2
       *
       * @var array The class varArray2
       */
      protected $varArray2 = array();
      
      /**
       * @var boolean The varWithWrongType. Boolean will be put in the type.
       */
      protected $varWithWrongType = array();
      
      /**
       * Header for instance3
       *
       * Info in a list:
       *
       * - unordered list item 1
       * - unordered list item 2
       *
       * @deprecated instance3 will not be used anymore
       * @see \DemoVar::$instance4
       * @see http://example.com/my/bar Documentation of Foo.
       *
       * @var array The class instance3
       */
      protected $instance3 = array();
      
      /**
       * Header for instance4
       *
       * Info in a list:
       *
       * - unordered list item 1
       * - unordered list item 2
       *
       * # ordered list item 1
       * # ordered list item 2
       *
       * @see \DemoVar
       *
       * @var array $instance4 The class instance4
       */
      protected $instance4 = array();
      
      /**
       * Constructor
       *
       * @uses \DemoVar::init()
       */
      public function __construct()
      {
         $this->init();
      }
      
      /**
       * Initialize the object.
       *
       * This method will be called by the constructor.
       *
       * @return \DemoVar
       */
      public function init()
      {
         return $this;
      }
   }
