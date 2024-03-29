Definition of a 'Type'
======================

Many tags use a Type as part of their definition (such as the @return tag).
These types differ from the official PHP definition to be able to represent all
kinds of data.

A complete definition will be given of these types and what they represent in
the following sections.

ABNF
----

::

    type-expression          = 1*(array-of-type-expression|array-of-type|type ["|"])
    array-of-type-expression = "(" type-expression ")[]"
    array-of-type            = type "[]"
    type                     = class-name|keyword
    class-name               = 1*CHAR
    keyword                  = "string"|"integer"|"int"|"boolean"|"bool"|"float"
                               |"double"|"object"|"mixed"|"array"|"resource"|"scalar"
                               |"void"|"null"|"callable"|"false"|"true"|"self"

When a Type is used the user will expect a value, or set of values, as
detailed below.

Atomic (singular) type
----------------------

The supported atomic types are either a *valid class name* or *keyword*.

Valid Class Name
~~~~~~~~~~~~~~~~

A valid class name seen from the context where this type is mentioned. Thus
this may be either a Fully Qualified Class Name (FQCN) or if present in a
namespace a local name.

The element to which this type applies is either an instance of this class
or an instance of a class that is a (sub-)child to the given class.

Example:

.. code-block:: php
   :linenos:

    @param \My\Namespace\Class
    @return Exception

Keyword
~~~~~~~

A keyword defining the purpose of this type. Not every element is determined
by a class but still worth of a classification to assist the developer in
understanding the code covered by the PHPDoc.

.. note::

    Most of these keywords are allowed as class names in PHP and as
    such are hard to distinguish from real classes. As such the keywords MUST
    be lowercase, as most class names start with an uppercase first character,
    and you SHOULD NOT use classes with these names in your code.

    There are more reasons to not name classes with the names of these
    keywords but that falls beyond the scope of this specification.

The following keywords are recognized:

**string**
    the element to which this type applies is a string of
    binary characters.

**integer** or **int**
    the element to which this type applies is a whole
    number or integer.

**boolean** or **bool**
    the element to which this type applies only has
    state true or false.

**float** or **double**
    the element to which this type applies is a continuous, or real, number.

**object**
    the element to which this type applies is the instance of an
    undetermined class.

**mixed**
    the element to which this type applies can be of any type as
    specified here. It is not known on compile time which type will be used.

**array**
    the element to which this type applies is an array of values,
    see the section on :ref:`arrays` for more details.

**resource**
    the element to which this type applies is a resource per
    the definition of PHP at
    https://www.php.net/language.types.resource.

**void**
    this type is commonly only used when defining the return type of a
    method or function.
    The basic definition is that the element indicated with this type does not
    contain a value and the user should not rely on any retrieved value.

    For example:

    .. code-block:: php
       :linenos:

        /**
         * @return void
         */
        function outputHello()
        {
            echo 'Hello world';
        }

    In the example above no return statement is specified and thus is the return
    value not determined.

    Example 2:

    .. code-block:: php
       :linenos:

        /**
         * @param boolean $quiet when true 'Hello world' is not echo-ed.
         *
         * @return void
         */
        function outputHello($quiet)
        {
            if ($quiet) {
                return;
            }
            echo 'Hello world';
        }

    In this example the function contains a return statement without a given
    value. Because there is no actual value specified does this also constitute
    as type 'void'.

**null**
    the element to which this type applies is a NULL value or, in
    technical terms, does not exist.

    A big difference compared to void is that this type is used in any situation
    where the described element may at any given time contain an explicit NULL
    value.

    Example:

    .. code-block:: php
       :linenos:

        /**
         * @return null
         */
        function foo()
        {
            echo 'Hello world';
            return null;
        }

    This type is commonly used in conjunction with another type to indicate that
    it is possible that nothing may be returned.

    Example:

    .. code-block:: php
       :linenos:

        /**
         * @param boolean $create_new When true returns a new stdClass.
         *
         * @return stdClass|null
         */
        function foo($create_new)
        {
            if ($create_new) {
                return new stdClass();
            }

            return null;
        }

**callable**
    the element to which this type applies is a pointer to a
    function call. This may be any type of callback as defined in the PHP manual
    at https://www.php.net/language.types.callable.

    Unlike the php language construct is this type is a bit more extended following the static analysis tools
    definition of a callable. This means that phpdoc will allow you to add more information about a callable using
    the following syntax:

    .. code-block:: php

        callable(int $a, string $b): bool

    Parameters and return types are optional and MAY be omitted and do follow the same rules as the type expressions.
    For parameters the name of the parameter is optional and MAY be omitted.

    Variadic parameters are supported and MAY be used in the following way:

    .. code-block:: php

        callable(int ...$a): bool
        callable(int...): bool

    .. note::

        This type is not fully supported in PHPDoc 3.3.0 and earlier.

**false** or **true**
    the element to which this type applies will have
    the value true or false. No other value will be returned from this
    element.

        This type is commonly used in conjunction with another type to indicate
        that it is possible that true or false may be returned instead of an
        instance of the other type.

**self**
    the element to which this type applies is of the same Class,
    or any of its children, as which the documented element is originally
    contained.

    For example:

        Method C() is contained in class A. The DocBlock states
        that its return value is of type `self`. As such method C()
        returns an instance of class A.

    This may lead to confusing situations when inheritance is involved.

    For example (previous example situation still applies):

        Class B extends Class A and does not redefine method C(). As such
        it is possible to invoke method C() from class B.

    In this situation ambiguity may arise as `self` could be interpreted as
    either class A or B. In these cases `self` MUST be interpreted as being
    an instance of the Class where the DocBlock containing the `self` type
    is written or any of its child classes.

    In the examples above `self` MUST always refer to class A or B, since
    it is defined with method C() in class A.

    If method C() was to be redefined in class B, including the type
    definition in the DocBlock, then `self` would refer to class B or any
    of its children.

**scalar**
    the element to which this type applies is of any scalar type
    specifically: *string*, *float*, *int*, *bool*

Multiple types
--------------

When the Type consists of multiple (sub-)types then these MUST be
separated with the vertical bar sign (|).

For example:

.. code-block:: php
   :linenos:

    @return int|null

Arrays
------

The value represented by Type can be an array. The type MUST be defined
following the format of one of the following options:

1. **unspecified**, no definition of the contents of the represented array is given.
   Example: ``@return array``

2. **specified containing a single type**, the Type definition informs
   the reader of the type of each array element. Only one Type is then
   expected as element for a given array.

   Example: ``@return int[]``

   Please note that *mixed* is also a single type and with this keyword it is
   possible to indicate that each array element contains any possible type.

3. **specified containing multiple types**, the Type definition informs the reader
   of the type of each array element. Each element can be of any of the given
   types.
   Example: ``@return (int|string)[]``
