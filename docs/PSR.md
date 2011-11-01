PSR-n: PHPDoc
=============

Author:           Mike van Riel <mike.vanriel@naenius.com>
Acknowledgements: The authors wish to thank the people who commented on
                  various versions of this proposal.
Obsoletes:        de-facto PHPDoc standard (http://www.phpdoc.org)

Table of Contents
-----------------

    1. Introduction
    1.1. History
    2. Conventions Used in This Document
    3. Definitions
    4. Basic principles
    5. The PHPDoc format
    5.1. short-description
    5.2. long-description
    5.3. tags
    5.3.1. tag-name
    5.3.2. tag-signature
    5.4. Examples
    6. Inheritance
    7. Tags
    Appendix A. Types

1. Introduction
---------------

## 1.1. History

2. Conventions Used in This Document
------------------------------------

The key words "MUST", "MUST NOT", "REQUIRED", "SHALL", "SHALL NOT",
"SHOULD", "SHOULD NOT", "RECOMMENDED", "MAY", and "OPTIONAL" in this
document are to be interpreted as described in RFC 2119.

3. Definitions
--------------

* "PHPDoc" is a section of documentation which provides information on several
  aspects of a "Structural Element".

* "Structural element" is a collection of Programming Constructs which SHOULD be
  preceded by a DocBlock. The collection contains the following constructs:

  * namespace
  * require(_once)
  * include(_once)
  * class
  * interface
  * trait
  * function
  * property
  * constant

* "DocComment" is a special type of comment which starts with `/**`, ends
  with `*/` and may contain any number of lines in between. Every line should
  start with an asterisk; which is aligned with the first asterisk of the
  opening clause.

  Single line example:

  /** <...> */

  Multiline example:
  /**
   * <...>
   */

* "DocBlock" is a "DocComment" containing a single "PHPDoc" and represents the
  basic in-source representation.

* "Tag"

* "Annotation"

* "Type" is the determination of what type of data is associated with an element.
  This is commonly used when determining the exact value of a arguments, constants,
  properties and more.

  See Appendix A for more detailed information about types.


4. Basic principles
-------------------

* A PHPDoc MUST always be contained in a "DocComment", the combination of these
  two is called a "DocBlock".

* A DocBlock MUST precede a "Structural element" or be placed at the top of a
  PHP source code file.

5. The PHPDoc format
--------------------

The PHPDoc format has the following ABNF (RFC 5234) definition:

    PHPDoc            = [short-description] [long-description] [tags]
    short-description = *CHAR ("." 1*CRLF / 2*CRLF)
    long-description  = 1*(CHAR / inline-tag) 1*CRLF ; any amount of characters
                                                     ; with inline tags inside
    tags              = *(tag 1*CRLF)
    inline-tag        = "{" tag "}"
    tag               = "@" tag-name [tag-details]
    tag-name          = (ALPHA / "\") *(ALPHA / DIGIT / "\" / "-" / "_")
    tag-details       = *" " (" " tag-description / tag-signature)
    tag-description   = 1*CHAR
    tag-signature     = "(" *tag-argument ")"
    tag-argument      = *" " 1*CHAR [","] *" "

Examples of use are included in chapter 5.4.

### 5.1. short-description

A short description MUST contain an abstract of the "Structural element"
defining the purpose. It is recommended for short descriptions to span a single
line or at most two but not more than that.

A short description must end with either a full stop (.) followed by a line
break or two sequential line breaks.

If a long description is provided then it MUST be preceded by a short
description. Otherwise the long description will be considered being the short
description until the stop of the short description is encountered.

Tags do not necessarily have to be preceded by a short description.

### 5.2. long-description

The long description is OPTIONAL but SHOULD be included when the
"Structural element", which this DocBlock precedes, contains more operations, or
more complex operations, then can be described in the short description alone.

Any application parsing the long description SHOULD support the Markdown
mark-up language for this field so that it is possible for the author to provide
formatting and a clear way of representing code examples.

Common uses for the long description are (amongst others):

* To provide more detail then the short description on what this method does to
  the casual reader.
* To specify of what child elements an input or output array, or object, is
  composed.
* To provide a set of common use cases or scenarios in which the
  "Structural element" may be applied.

### 5.3. tags

Tags provide a way for authors to supply concise meta-data regarding the
succeeding "Structural element". They commonly consist of a name followed by
white-space and a description. The description MAY span multiple lines and MAY
follow a strict format dictated by the type of tag, as indicated by its name.

The meta-data supplied by tags could result in a change of behaviour of the
succeeding "Structural element", in which case the term "Annotation" is
commonly used instead of "Tag".

A variation of this is where, instead of a description, a tag-signature is used;
in most cases will the tag be in fact an annotation. The tag-signature is able
to provide the annotation with parameters regarding to operation.

if a tag-signature is present then there MUST NOT be a description present in
the same tag.

Annotations will not be described in further detail in this specification as
this falls beyond the scope. This specification provides a basis on top of which
annotations may be implemented.

#### 5.3.1. tag-name

Tag names indicate what type of information is represented by this tag or, in
case of annotations, which behaviour must be injected into the succeeding
"Structural element".

It is supported to introduce a set of tags designed specifically for an
individual application or subset of applications (and thus not covered by this
specification).

These tags, or annotations, MUST provide a namespace by either
* prefixing the tag name with a PHP-style namespace, or by
* prefixing the tag name with a single vendor-name followed by a hyphen.

Example of a tag name prefixed with a php-style namespace (the prefixing slash
is OPTIONAL):

    @\Doctrine\Orm\Mapping\Entity()

Example of a tag name prefixed with a vendor name and hyphen:

    @docblox-event transformer.transform.pre

Tag names that are not prefixed with a vendor or namespace MUST be described in
this specification (see chapter 7) and/or any official addendum.

#### 5.3.2. tag-signature

Tag signatures are commonly used for annotations to supply additional meta-data
specific to the current tag.

The supplied meta-data can influence the behavior of the owning annotation and
as such influence the behavior of the succeeding "Structural Element".

The contents of a signature are to be determined by the tag type (as described
in the tag-name) and fall beyond the scope of this specification. However, a
tag-signature MUST NOT be followed by a description or other form of meta-data.

### 5.4. Examples

The following examples serve to illustrate the basic use of DocBlocks; it is
adviced to read through the list of tags in chapter 7.

A complete example could look like the following example:

    /**
     * This is a short description.
     *
     * This is a long description. It may span multiple lines
     * or contain 'code' examples using the _Markdown_ markup
     * language.
     *
     * @see Markdown
     *
     * @param int        $parameter1 a parameter description.
     * @param \Exception $parameter2 another parameter description.
     *
     * @\Doctrine\Orm\Mapper\Entity()
     *
     * @return string
     */
    function test($parameter1, $parameter2)
    {
    }

It is also allowed to omit the long description:

    /**
     * This is a short description.
     *
     * @see Markdown
     *
     * @param int        $parameter1 a parameter description.
     * @param \Exception $parameter2 another parameter description.
     *
     * @\Doctrine\Orm\Mapper\Entity()
     *
     * @return string
     */
    function test($parameter1, $parameter2)
    {
    }

Or even omit the tags section as well (though in the following example is not
encouraged as you are missing information on the parameters and return value):

    /**
     * This is a short description.
     */
    function test($parameter1, $parameter2)
    {
    }

A DocBlock may also span a single line as shown in the following example.

    /** @var \ArrayObject $array */
    public $array = null;

6. Inheritance
--------------

PHPDoc's also have the ability to inherit information when the succeeding
"Structural element" has a super-element (such as a super-class or a method with
the same name in a super-class).

Every "Structural Element" MUST inherit the following PHPDoc parts by default:

* Short description
* Long description
* A specific subset of tags
  * @version
  * @author
  * @copyright

Each specific "Structural Element" MUST also inherit a specialized subset as
defined in the sub-chapters.

The PHPDoc parts MUST NOT be inherited when a replacement is available in the
sub-element. The exception to this rule is when the {@inheritdoc} inline tag is
present in the long description. When present the parser MUST insert the
super-element's long description at the location of the {@inheritdoc} inline
tag.

Inheritance takes place from the root of a class hierarchy graph to its leafs.
This means that anything inherited in the bottom of the tree MUST 'bubble' up to
the top unless overridden.

### 6.1. Class

In addition to the inherited information, as defined in the chapter's root, MUST
a class inherit the following tags.

* @package
* @subpackage

The @subpackage is MUST NOT be inherited if the @package annotation of the
super
-

### 6.2. Function / method

### 6.3. Constant / property

7. Tags
-------

### @api

### @category
deprecated

### @internal

### @ignore

### @link
deprecated

### @package

### @param

### @return

### @see

### @subpackage
deprecated

### @throws

### @type

### @uses

### @var
deprecated

Appendix A. Types
-----------------

When a "Type" is used the user will expect a value, or set of values, as
detailed below.

When the "Type" may consist of multiple types then these MUST be separated
with the pipe (|) sign. Any application supporting this PSR MUST recognize this
and split the "Type" before processing.
For example: `@return int|null`

When the "Type" is in fact an array of "Type" then this is represented by adding
an empty closed pair of brackets as suffix.
For example: `@return int[]`

The supported atomic types are either a *valid class name* or *keyword*:

### Valid class name

a valid class name seen from the context where this type is mentioned. Thus
 this may be either a Fully Qualified Class Name (FQCN) or if present in a
 namespace a local name.

    It is RECOMMENDED for applications to expand any local name into a FQCN
    for easier processing and comparisons.

 The element to which this type applies is either an instance of this class
 or an instance of a class that is a (sub-)child to the given class.

    Due to the above nature it is RECOMMENDED for applications that
    collect and shape this information to show a list of child classes
    with each representation of the class. This would make it obvious
    for the user which classes are acceptable as type.

### Keyword

a keyword defining the purpose of this type. Not every element is determined
by a class but still worth of a classification to assist the developer in
understanding the code covered by the DocBlock.

    Note: most of these keywords are allowed as class names in PHP and as
    such are hard to distinguish from real classes. As such MUST the keywords
    be lowercase, as most class names start with an uppercase first character,
    and you SHOULD NOT use classes with these names in your code.

    There are more reasons to not name classes with the names of these
    keywords but that falls beyond the scope of this specification.

The following keywords are recognized by this PSR:
1.  'string', the element to which this type applies is a string of
    binary characters.
2.  'integer', the element to which this type applies is a whole number or integer.
3.  'boolean', the element to which this type applies only has state true or false.
4.  'float' or 'double', the element to which this type applies is continuous
    or real number.
5.  'object', the element to which this type applies is the instance of
    undetermined class.
6.  'mixed', the element to which this type applies can be of any type as
    specified here. It is now know on compile time which type will be used.
7.  'array', the element to which this type applies is an array of values.
8.  'resource', the element to which this type applies is a resource per
    the definition of PHP types.
9.  'void', commonly only used with return values; means that no value is returned.
10. 'null', the element to which this type applies is a NULL value or, in
    technical terms, does not exist.
11. 'callback', the element to which this type applies is a pointer to a
    function call. This may be an array containing a class name and method
    name, an array containing an object and method name, closure or any other
    definition as provided by PHP itself.
12. 'false' or 'true', the element to which this type applies will have
    the value true or false. No other value will be returned from this
    element.
13. 'self', the element to which this type applies is of the same Class,
    or any of its children, as which the documented element is originally
    contained.

    For example:

        Method C() is contained in class A. The DocBlock states
        that its return value is of type `self`. As such does method C()
        return an instance of class A.

    This may lead to confusing situations when inheritance is involved.

    For example (previous example situation still applies):

        Class B extends Class A and does not redefine method C(). As such
        it is possible to invoke method C() from class B.

    In this situation ambiguity may arise as `self` could be interpreted as
    either class A or B. In these cases `self` MUST be interpreted as being
    an instance of the Class where the DocBlock containing the `self` type
    is written or any of its child classes.

    In the examples above MUST `self` always refer to class A or B, since
    it is defined with method C() in class A.

    If method C() was to be redefined in class B, including the type
    definition in the DocBlock, then `self` would refer to class B or any
    of its children.

        Due to the above nature it is RECOMMENDED for applications that
        collect and shape this information to show a list of child classes
        with each representation of the class. This would make it obvious
        for the user which classes are acceptable as type.
