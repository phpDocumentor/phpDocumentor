Types
=====

Several tags require or support the use of types to represent the type of value contained in the associated element. An
example of this is the ``@param`` tag, which identifies the type of an argument with a method or function.

This guide serves to provide more insight in which types are available, how they may be combined and even how to define
arrays with specific types for its elements.

Types of types
--------------

A type can either be

* a class name, either Fully Qualified or aliases; such as ``\DateTime`` or ``Entity`` (with a namespace alias elsewhere
  in the same source file).
* a keyword for a primitive in PHP, such as ``int`` or ``string``.
* a special keyword specific to the PHPDoc Standard, such as ``false`` or ``mixed``.

Class Name
~~~~~~~~~~

When you want to refer to another object you can follow the same rules as PHP applies to its source code with regards to
resolving namespaces.

This means that any class may be addressed

* using its Fully Qualified Class Name (FQCN), which means that the class has a prefixing slash to indicate it is the
  full name of the class, e.g. ``\phpDocumentor\Descriptor\ClassDescriptor``.
* by a relative class name, when you omit the prefixing slash then phpDocumentor will prepend the current namespace onto
  the class definition, e.g. ``Descriptor\ClassDescriptor`` would become ``\phpDocumentor\Descriptor\ClassDescriptor``
  when you're tag declaration is inside the ``phpDocumentor`` namespace.
* using a namespace/object alias, if you define an alias for a namespace, or import it, using the **use** keyword then
  it becomes available for use.

  So suppose you have the following **use** statement in your source file:

      use phpDocumentor\Descriptor\ParamDescriptor as Param

  Now you can refer to the class above as ``Param`` from any tag that refers to a :term:`Type`.

.. warning::

   Some older annotations, such as PHPUnit's ``@covers`` only support Qualified Class Names (which means a complete
   class name without the prefixing slash); these annotations also do not support the resolving of namespaces. Keep
   this in mind when working with tags as it may be confusing at some point.

Of course this is only a short introduction on class name resolution with PHP. When you want to know more on how PHP
resolves class names, please read the `php manual on namespacing`_.


.. _php manual on namespacing: http://php.net

Primitives
~~~~~~~~~~

The PHPDoc Standard, and thus phpDocumentor, can refer to all primitive types in PHP.

Here is a full listing;

string
    A piece of text of an unspecified length.

int or integer
    A whole number that may be either positive or negative.

float
    A real, or decimal, number that may be either positive or negative.

bool or boolean
    A variable that can only contain the state 'true' or 'false'.

array
    A collection of variables of unknown type. It is possible to specify the types of array members, see the chapter
    on arrays for more information.

resource
    A file handler or other system resource as described in the PHP manual.

null
    The value contained, or returned, is literally null. This type is not to be confused with void, which is the total
    absence of a variable or value (usually used with the ``@return`` tag).

callable
    A function or method that can be passed by a variable, see the PHP manual for more information on callables.

Keywords
~~~~~~~~

The PHPDoc Standard also describes several keywords that are not native to PHP but are found to be often used or are
representations of situations that are convenient to describe.

mixed
    A value with this type can be literally anything; the author of the documentation is unable to predict which type
    it will be.

void
    *This is not the value that you are looking for.* The tag associated with this type does not intentionally return
    anything. Anything returned by the associated element is incidental and not to be relied on.

object
    An object of any class is returned,

false or true
    An explicit boolean value is returned; usually used when a method returns 'false' or something of consequence.

self
    An object of the class where this type was used, if inherited it will still represent the class where it was
    originally defined.

static
    An object of the class where this value was consumed, if inherited it will represent the child class. (see late
    static binding in the PHP manual).

$this
    This exact object instance, usually used to denote a fluent interface.

Arrays
------

In the previous chapter you had seen that the 'array' keyword is supported by phpDocumentor, but this keyword says
little about the contents of that array. Usually you have an array with a specific purpose and hence elements of one
or at most two different Types.

For phpDocumentor to be able to help you determine which element Types are contained in an array you can declare a Type,
such as ``\DateTime``, and suffix it with an opening and closing square bracket. The brackets inform you, and several
tools, that this is an array of that Type.

Some examples::

    /** @var \DateTime[] An array of DateTime objects. */
    /** @var string[] An array of string objects. */
    /** @var callable[] An array with callback functions or methods. */

.. note::

   This notation is inspired by the way some strong-types languages, such as Java and C/C++, declare arrays.

Aside from phpDocumentor there are various tools that understand this notation and use it to aid in their functioning.
Most IDEs, such as phpStorm, can apply auto-completion or warn you of non-existing methods by reading this information
and inferring the types of variables, properties and even method return values.

Multiple types combined
-----------------------

Sometimes an element may accept or return a value that can be any of a limited set of Types. An example of this is a
getter-method that returns an object *or* null if no object was found.

To be able to track which types may be used in a value you can use the pipe, or OR, (|) operator to separate each type
that the associated value may be.

In the following example a method, or function, will return either a string or null as value::

    /** @return string|null */

Most IDEs will recognize this format as well and offer auto-completion based on all types mentioned in the DocBlock;
so, for example, the following property will be treated both as an ArrayObject (exposing all its methods) and an
array of DateTime objects::

    /**
     * @var \ArrayObject|\DateTime[]
     */
    $dates = array()

Related topics
--------------

* :doc:`../getting-started/your-first-set-of-documentation`, for an introduction in writing DocBlocks.
* :doc:`../references/phpdoc/types`, for a complete, and more elaborate, reference on types and their syntax.
