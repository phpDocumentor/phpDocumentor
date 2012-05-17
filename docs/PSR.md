PSR-n: PHPDoc
=============

Author(s):

    Mike van Riel (@mvriel) <mike.vanriel@naenius.com>

Acknowledgements:

    The authors wish to thank Chuck Burgess (@ashnazg),
    Gary Jones (@GaryJ) and all other people who commented on
    various versions of this proposal.

Obsoletes:

    De-facto PHPDoc Standard (http://www.phpdoc.org)

## Table Of Contents

    1. Introduction
    2. Conventions Used In This Document
    3. Definitions
    4. Basic Principles
    5. The PHPDoc Format
      5.1. Short Description
      5.2. Long Description
      5.3. Tags
        5.3.1. Tag Name
        5.3.2. Tag Signature
      5.4. Examples
    6. Inheritance
      6.1. Class Or Interface
      6.2. Function Or Method
      6.3. Constant Or Property
    7. Tags
      7.1.  @api
      7.2.  @author
      7.3.  @category [deprecated]
      7.4.  @copyright
      7.5.  @deprecated
      7.6.  @example
      7.7.  @global
      7.8.  @internal
      7.9.  @license
      7.10. @link
      7.11. @method
      7.12. @package
      7.13. @param
      7.14. @property
      7.15. @return
      7.16. @see
      7.17. @since
      7.18. @subpackage [deprecated]
      7.19. @throws
      7.20. @todo
      7.21. @type
      7.22. @uses
      7.23. @var
      7.24. @version
    Appendix A. Types
    Appendix B. Differences Compared With The De-facto PHPDoc Standard

## 1. Introduction

The main purpose of this PSR is to provide a complete and formal definition of
the PHPDoc standard. This PSR deviates from its predecessor, the de-facto PHPDoc
Standard associated with [phpDocumentor 1.x](http://www.phpdoc.org), to provide
support for newer features in the PHP language and to address some of the
shortcomings of its predecessor.

This document SHALL NOT:

* Describe a standard for implementing annotations via PHPDoc. Although it does
  offer versatility which makes it possible to create a subsequent PSR based on
  current practices. See [chapter 5.3](#53-tags) for more information on this
  topic.
* Describe best practices or recommendations for Coding Standards on the
  application of the PHPDoc standard. This document is limited to a formal
  specification of syntax and intention.

## 2. Conventions Used In This Document

The key words "MUST", "MUST NOT", "REQUIRED", "SHALL", "SHALL NOT",
"SHOULD", "SHOULD NOT", "RECOMMENDED", "MAY", and "OPTIONAL" in this
document are to be interpreted as described in
[RFC 2119](http://www.ietf.org/rfc/rfc2119.txt).

## 3. Definitions

* "PHPDoc" is a section of documentation which provides information on several
  aspects of a "Structural Element".

  > It is important to note that the PHPDoc and the DocBlock are two separate
  > entities. The DocBlock is the combination of a DocComment, which is a type
  > of comment, and a PHPDoc entity. It is the PHPDoc entity that describes the
  > Short Description, Long Description and Tags.

* "Structural Element" is a collection of Programming Constructs which SHOULD be
  preceded by a DocBlock. The collection contains the following constructs:

  * namespace
  * require(_once)
  * include(_once)
  * class
  * interface
  * trait
  * function (including methods)
  * property
  * constant
  * variables, both local and global scope.

  It is RECOMMENDED to precede a "Structural Element" with a DocBlock with its
  definition and not with each usage.

  Example:

  ```php
  /** @type int This is a counter. */
  $int = 0;

  // there should be no docblock here
  $int++;
  ```

  Or:

  ```php
  /**
   * This class acts as an example on where to position a DocBlock.
   */
  class Foo
  {
      /** @type string|null Should contain a description if available */
      protected $description = null;

      /**
       * This method sets a description.
       *
       * @param string $description A text with a maximum of 80 characters.
       *
       * @return void
       */
      public function setDescription($description)
      {
          // there should be no docblock here
          $this->description = $description;
      }
  }
  ```

  An example of use that falls beyond the scope of this Standard is to document
  the variable in a foreach explicitly; many IDEs use this information to help
  you with auto-completion.

  This Standard does not cover this specific instance as a foreach is not
  considered to be a "Structural Element" but a Control Flow structure.

  ```php
  /** @type \Sqlite3 $sqlite */
  foreach($connections as $sqlite) {
      // there should be no docblock here
      $sqlite->open('/my/database/path');
      <...>
  }
  ```

* "DocComment" is a special type of comment which starts with `/**`, ends
  with `*/` and may contain any number of lines in between.
  In case a DocComment spans multiple lines should every line start with an
  asterisk that is aligned with the first asterisk of the opening clause.

  Single line example:

  ```php
  /** <...> */
  ```

  Multiline example:

  ```php
  /**
   * <...>
   */
  ```

* "DocBlock" is a "DocComment" containing a single "PHPDoc" and represents the
  basic in-source representation.

* "Tag" is a single piece of meta information regarding a "Structural Element"
  or a component thereof.

* "Type" is the determination of what type of data is associated with an element.
  This is commonly used when determining the exact values of arguments, constants,
  properties and more.

  See Appendix A for more detailed information about types.

## 4. Basic Principles

* A PHPDoc MUST always be contained in a "DocComment"; the combination of these
  two is called a "DocBlock".

* A DocBlock MUST precede a "Structural Element"

  > An exception to this principle is the File-level DocBlock which must be
  > placed at the top of a PHP source code file.

## 5. The PHPDoc Format

The PHPDoc format has the following [ABNF](http://www.ietf.org/rfc/rfc5234.txt)
definition:

    PHPDoc            = [short-description] [long-description] [tags]
    short-description = *CHAR ("." 1*CRLF / 2*CRLF)
    long-description  = 1*(CHAR / inline-tag) 1*CRLF ; any amount of characters
                                                     ; with inline tags inside
    tags              = *(tag 1*CRLF)
    inline-tag        = "{" tag "}"
    tag               = "@" tag-name [tag-details]
    tag-name          = (ALPHA / "\") *(ALPHA / DIGIT / "\" / "-" / "_")
    tag-details       = *SP (SP tag-description / tag-signature)
    tag-description   = 1*CHAR
    tag-signature     = "(" *tag-argument ")"
    tag-argument      = *SP 1*CHAR [","] *SP

Examples of use are included in chapter 5.4.

### 5.1. Short Description

A short description MUST contain an abstract of the "Structural Element"
defining the purpose. It is recommended for short descriptions to span a single
line or at most two but not more than that.

A short description must end with either a full stop (.) followed by a line
break or two sequential line breaks.

If a long description is provided then it MUST be preceded by a short
description. Otherwise the long description will be considered being the short
description until the stop of the short description is encountered.

Tags do not necessarily have to be preceded by a short description.

### 5.2. Long Description

The long description is OPTIONAL but SHOULD be included when the
"Structural Element", which this DocBlock precedes, contains more operations, or
more complex operations, than can be described in the short description alone.

Any application parsing the long description SHOULD support the Markdown
mark-up language for this field so that it is possible for the author to provide
formatting and a clear way of representing code examples.

Common uses for the long description are (amongst others):

* To provide more detail to the casual reader than the short description on
  what this method does.
* To specify of what child elements an input or output array, or object, is
  composed.
* To provide a set of common use cases or scenarios in which the
  "Structural Element" may be applied.

### 5.3. Tags

Tags provide a way for authors to supply concise meta-data regarding the
succeeding "Structural Element". They commonly consist of a name followed by
white-space and a description. The description MAY span multiple lines and MAY
follow a strict format dictated by the type of tag, as indicated by its name.

The meta-data supplied by tags could result in a change of behaviour of the
succeeding "Structural Element", in which case the term "Annotation" is
commonly used instead of "Tag".

A variation of this is where, instead of a description, a tag-signature is used;
in most cases the tag will in fact be an annotation. The tag-signature is able
to provide the annotation with parameters regarding its operation.

If a tag-signature is present then there MUST NOT be a description present in
the same tag.

Annotations will not be described in further detail in this specification as
this falls beyond the scope. This specification provides a basis on top of which
annotations may be implemented.

#### 5.3.1. Tag Name

Tag names indicate what type of information is represented by this tag or, in
case of annotations, which behaviour must be injected into the succeeding
"Structural Element".

In support of annotations, it is allowable to introduce a set of tags designed
specifically for an individual application or subset of applications (and thus
not covered by this specification).

These tags, or annotations, MUST provide a namespace by either
* prefixing the tag name with a PHP-style namespace, or by
* prefixing the tag name with a single vendor-name followed by a hyphen.

Example of a tag name prefixed with a php-style namespace (the prefixing slash
is OPTIONAL):

    @\Doctrine\Orm\Mapping\Entity()

> *Note*: The PHPDoc Standard DOES NOT make assumptions on the meaning of a tag
> unless specified in this document or subsequent additions or extensions.
>
> This means that you CAN use namespace aliases as long as a prefixing namespace
> element is provided. Thus the following is legal as well:
>
>     @Mapping\Entity()
>
> Your own library or application may check for namespace aliases and make a
> FQCN from this; this has no impact on this standard.

> *Important*: Individual Documentation Generation Applications (DGAs) MAY
> interpret namespaces that are registered with that application and apply
> custom behaviour.

Example of a tag name prefixed with a vendor name and hyphen:

    @phpdoc-event transformer.transform.pre

Tag names that are not prefixed with a vendor or namespace MUST be described in
this specification (see chapter 7) and/or any official addendum.

#### 5.3.2. Tag Signature

Tag signatures are commonly used for annotations to supply additional meta-data
specific to the current tag.

The supplied meta-data can influence the behavior of the owning annotation and
as such influence the behavior of the succeeding "Structural Element".

The contents of a signature are to be determined by the tag type (as described
in the tag-name) and fall beyond the scope of this specification. However, a
tag-signature MUST NOT be followed by a description or other form of meta-data.

### 5.4. Examples

The following examples serve to illustrate the basic use of DocBlocks; it is
advised to read through the list of tags in chapter 7.

A complete example could look like the following example:

```php
/**
 * This is a short description.
 *
 * This is a long description. It may span multiple lines
 * or contain 'code' examples using the _Markdown_ markup
 * language.
 *
 * @see Markdown
 *
 * @param int        $parameter1 A parameter description.
 * @param \Exception $e          Another parameter description.
 *
 * @\Doctrine\Orm\Mapper\Entity()
 *
 * @return string
 */
function test($parameter1, $e)
{
}
```

It is also allowed to omit the long description:

```php
/**
 * This is a short description.
 *
 * @see Markdown
 *
 * @param int        $parameter1 A parameter description.
 * @param \Exception $parameter2 Another parameter description.
 *
 * @\Doctrine\Orm\Mapper\Entity()
 *
 * @return string
 */
function test($parameter1, $parameter2)
{
}
```

Or even omit the tags section as well (though in the following example is not
encouraged as you are missing information on the parameters and return value):

```php
/**
 * This is a short description.
 */
function test($parameter1, $parameter2)
{
}
```

A DocBlock may also span a single line as shown in the following example.

```php
/** @var \ArrayObject $array */
public $array = null;
```

## 6. Inheritance

PHPDoc's also have the ability to inherit information when the succeeding
"Structural Element" has a super-element (such as a super-class or a method with
the same name in a super-class or implemented in a super-interface).

Every "Structural Element" MUST inherit the following PHPDoc parts by default:

* [Short description](#51-short-description)
* [Long description](#52-long-description)
* A specific subset of [tags](#53-tags)
  * [@version](#724-version)
  * [@author](#72-author)
  * [@copyright](#74-copyright)

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

> Note: a special circumstance here would be when the Long Description must be
> overridden but the Short Description should stay intact. It would be difficult
> for a reader to distinguish which is overridden.
>
> In this case MUST the writer use the {@inheritdoc} inline tag as
> Short Description and override the Long Description with the intended text.
>
> Without the {@inheritdoc} inline tag MUST the reader interpret any text
> as if the Short Description would be overridden and MAY long description
> appear overridden if the block of text contains a Short Description ending
> as defined in the ABNF.

### 6.1. Class Or Interface

In addition to the inherited descriptions and tags as defined in this chapter's
root MUST a class or interface inherit the following tags:

* [@package](#712-package)

A class or interface SHOULD inherit the following deprecated tags if supplied:

* [@subpackage](#718-subpackage)

The @subpackage MUST NOT be inherited if the @package annotation of the
super-class (or interface) is not the same as the @package of the child class
(or interface).

Example:

```php
/**
 * @package    Framework
 * @subpackage Controllers
 */
class Framework_ActionController
{
    <...>
}

/**
 * @package My
 *
class My_ActionController extends Framework_ActionController
{
    <...>
}
```

In the example above the My_ActionController MUST not inherit the subpackage
_Controllers_.

### 6.2. Function Or Method

In addition to the inherited descriptions and tags as defined in this chapter's
root MUST a class or interface inherit the following tags:

* [@param](#713-param)
* [@return](#715-return)
* [@throws](#719-throws)

### 6.3. Constant Or Property

In addition to the inherited descriptions and tags as defined in this chapter's
root MUST a class or interface inherit the following tags:

* [@type](#721-type)

A constant or property SHOULD inherit the following deprecated tags if supplied:

* [@var](#723-var)

## 7. Tags

Unless specifically mentioned in the description MAY each tag occur zero or more
times in each "DocBlock".

### 7.1. @api

The @api tag is used to declare "Structural Elements" as being suitable for
consumption by third parties.

#### Syntax

    @api

#### Description

The @api tag represents those "Structural Elements" with a public visibility
which are intended to be the public API components for a library or framework.
Other "Structural Elements" with a public visibility serve to support the
internal structure and are not recommended to be used by the consumer.

The exact meaning of "Structural Elements" tagged with @api MAY differ per
project. It is however RECOMMENDED that all tagged "Structural Elements" SHOULD
NOT change after publication unless the new version is tagged as breaking
Backwards Compatibility.

#### Examples

```php
/**
 * This method will not change until a major release.
 *
 * @api
 *
 * @return void
 */
 function showVersion()
 {
    <...>
 }
```

### 7.2. @author

The @author tag is used to document the author of any "Structural Element".

#### Syntax

    @author [name] [<email address>]

#### Description

The @author tag can be used to indicate who has created a"Structural Element"
or has made significant modifications to it. This tag MAY also contain an
e-mail address. If an e-mail address is provided it MUST follow
the author's name and be contained in chevrons, or angle brackets, and MUST
adhere to the syntax defined in RFC 2822.

#### Examples

```php
/**
 * @author My Name
 * @author My Name <my.name@example.com>
 */
```

### 7.3. @category [deprecated]

The @category tag is used to organize groups of packages together but is
deprecated in favour of occupying the top-level with the @package tag.
As such usage of this tag is NOT RECOMMENDED.

#### Syntax

    @category [description]

#### Description

The @category tag was meant in the original de-facto Standard to group several
"Structural Elements" their @packages into one category. These categories could
then be used to aid in the generation of API documentation.

This was necessary since the @package tag as defined in the original Standard did
not contain more then one hierarchy level; since this has changed this tag SHOULD
NOT be used.

Please see the documentation for `@package` for details of its usage.

This tag MUST NOT occur more than once in a "DocBlock".

#### Examples

```php
/**
 * Page-Level DocBlock
 *
 * @category MyCategory
 * @package  MyPackage
 */
```

### 7.4. @copyright

The @copyright tag is used to document the copyright information of any
"Structural element".

#### Syntax

    @copyright <description>

#### Description

The @copyright tag defines who holds the copyright over the "Structural Element".
The copyright indicates with this tag applies to the "Structural Element" to
which it applies and all child elements unless otherwise noted.

The format of the description if governed by the coding standard of each
individual project. It is RECOMMENDED to mention the year or years which are
covered by this copyright and the organization involved.

#### Examples

```php
/**
 * @copyright 1997-2005 The PHP Group
 */
```

### 7.5. @deprecated

The @deprecated tag is used to indicate which 'Structural elements' are
deprecated and are to be removed in a future version.

#### Syntax

    @deprecated [<version>] [<description>]

#### Description

The @deprecated tag declares that the associated 'Structural elements' will
be removed in a future version as it has become obsolete or its usage is otherwise
not recommended.

This tag MAY also contain a version number up till which it is guaranteed to be
included in the software. Starting with the given version will the function be
removed or may be removed without further notice.

If is RECOMMENDED (but not required) to provide an additional description stating
why the associated element is deprecated.
If it is superceded by another method it is RECOMMENDED to add a @see tag in the
same 'PHPDoc' pointing to the new element.

#### Examples

```php
/**
 * @deprecated
 * @deprecated 1.0.0
 * @deprecated No longer used by internal code and not recommended.
 * @deprecated 1.0.0 No longer used by internal code and not recommended.
 */
```

### 7.6. @example

The @example tag is used to link to an external source code file which contains
an example of use for the current "Structural element". An inline variant exists
with which code from an example file can be shown inline with the Long
Description.

#### Syntax

    @example [URI] [<description>]

or inline:

    {@example [URI] [:<start>..<end>]}

#### Description

The example tag refers to a file containing example code demonstrating the
purpose and use of the current "Structural element". Multiple example tags may
be used per "Structural element" in case several scenarios are described.

The URI provided with the example tag is resolved according to the following
rules:

1. If a URI is proceeded by a scheme or root folder specifier such as `phar://`,
   `http://`, `/` or `C:\` then it is considered to be an absolute path.
2. If the URI is deemed relative and a location for the example files has been
   provided then the path relative to the given location is resolved.
3. If the previous path was not readable or the user has not provided a path
   than the application should try to search for a folder examples in the
   same folder as the source file featuring the example tag. If found then an
   attempt to resolve the path by combining the relative path given in the
   example tag and the found folder should be made.
4. If the application was unable to resolve a path given the previous rules than
   it should check if a readable folder 'examples' is found in the root folder
   of the project containing the "Structural Element" his source file.

   > The root folder of a project is the highest folder common to all files
   > that are being processed by a consuming application.

If a consumer intends to display the contents of the example file then it is
RECOMMENDED to use a syntax highlighting solution to improve user experience.

The rules as described above apply to both the inline as the normal tags. The
inline tag has 2 additional parameters with which to limit which lines of code
are shown in the Long Description. Due to this consuming applications MUST
show the example code in case an inline example tag is used.

The start and end argument may be omitted but the ellipsis should remain in
case either is used to give a clear visual indication. The same rules as
specified with the [substr](http://nl.php.net/manual/en/function.substr.php)
function of PHP are in effect with regards to the start and end limit.

> A consuming application MAY choose to support the limit format as used in the
> previous standard but it is deprecated per this PSR.
> The previous syntax was: {@example [URI] [<start>] [<end>]} and did not support
> the same rules as the substr function.

#### Examples

```php
/**
 * Counts the number of items.
 * {@example http://example.com/foo-inline.https:2..8}
 *
 * @example http://example.com/foo.phps
 *
 * @return integer Indicates the number of items.
 */
function count()
{
    <...>
}
```

### 7.7. @global

### 7.8. @internal

The @internal tag is used to denote that the associated "Structural Element" is
a structure internal to this application or library. It may also be used inside
a long description to insert a piece of text that is only applicable for
the developers of this software.

#### Syntax

    @internal

or inline:

    {@internal [description]}}

The inline version of this tag may, contrary to other inline tags, contain
text but also other inline tags. To increase readability and ease parsing should
the tag be terminated with a double closing brace, instead of a single one.

#### Description

The @internal tag can be used as counterpart of the @api tag, indicating that
the associated "Structural Element" is used purely for the internal workings of
this piece of software.

When generating documentation from PHPDoc comments it is RECOMMENDED to hide the
associated element unless the user has explicitly indicated that internal elements
should be included.

An additional use of @internal is to add internal comments or additional
description text inline to the Long Description. This may be done, for example,
to withhold certain business-critical or confusing information when generating
documentation from the source code of this piece of software.

#### Examples

Mark the count function as being internal to this project:

```php
/**
 * @internal
 *
 * @return integer Indicates the number of items.
 */
function count()
{
    <...>
}
```

```php
/**
 * Counts the number of Foo.
 *
 * {@internal Silently adds one extra Foo to compensate for lack of Foo }}
 *
 * @return integer Indicates the number of items.
 */
function count()
{
    <...>
}
```

### 7.9. @license

The @license tag is used to indicate which license is applicable for the associated
'Structural Elements'.

#### Syntax

    @license [<url>] [name]

#### Description

The @license tag provides the user with the name and URL of the license that is
applicable to 'Structural Elements' and any of their child elements.

It is NOT RECOMMENDED to apply @license tags to any 'PHPDoc' other than
file-level PHPDocs as this may cause confusion which license applies at which
time.

Whenever multiple licenses apply MUST there be one @license tag per applicable
license.

Instead of providing a URL MAY an identifier as identified in the
[SPDX Open Source License Registry](http://www.spdx.org/licenses/) be provided
and SHOULD this be interpreted as if having the URL mentioned in the registry.

#### Examples

```php
/**
 * @license MIT
 * @license http://www.spdx.org/licenses/MIT MIT License
 */
```

### 7.10. @link

The @link tag indicates a custom relation between the associated
"Structural Element" and a website, which is identified by an absolute URI.

#### Syntax

    @link [URI] [description]

or inline

   {@link [URI] [description]}

#### Description

The @link tag can be used to define a relation, or link, between the
"Structural Element", or part of the long description when used inline,
to an URI.

The URI MUST be complete and welformed as specified in RFC 2396.

The @link tag MAY have a description appended to indicate the type of relation
defined by this occurrence.

#### Examples

```php
/**
 * @link http://example.com/my/bar Documentation of Foo.
 *
 * @return integer Indicates the number of items.
 */
function count()
{
    <...>
}
```

```php
/**
 * This method counts the occurences of Foo.
 *
 * When no more Foo ({@link http://example.com/my/bar}) are given this
 * function will add one as there must always be one Foo.
 *
 * @return integer Indicates the number of items.
 */
function count()
{
    <...>
}
```

### 7.11. @method

The @method allows a class to know which 'magic' methods are callable.

#### Syntax

    @method [return type] [name]([type] [parameter], [...]) [description]

#### Description

The @method tag is used in situation where a class contains the __call() magic
method and defines some definite uses.

An example of this is a child class whose parent has a __call() to have dynamic
getters or setters for predefined properties. The child knows which getters and
setters need to be present but relies on the parent class to use the __call()
method to provide it. In this situation, the child class would have a @method
tag for each magic setter or getter method.

The @method tag allows the author to communicate the type of the arguments and
return value by including those types in the signature.

When the intended method does not have a return value then the return type MAY
be omitted; in which case 'void' is implied.

@method tags MUST NOT be used in a PHPDoc that is not associated with a
*class* or *interface*.

#### Examples

```php
class Parent
{
    public function __call()
    {
        <...>
    }
}

/**
 * @method string getString()
 * @method void setInteger(integer $integer)
 * @method setString(integer $integer)
 */
class Child extends Parent
{
    <...>
}
```

### 7.12. @package

The @package tag is used to categorize "Structural Elements" into logical
subdivisions.

#### Syntax

    @package [level 1]\[level 2]\[etc.]

#### Description

The @package tag can be used as a counterpart or supplement to Namespaces.
Namespaces provide a functional subdivision of "Structural Elements" where the
@package tag can provide a *logical* subdivision in which way the elements can
be grouped with a different hierarchy.

If, across the board, both logical and functional subdivisions are equal is
it NOT RECOMMENDED to use the @package tag, to prevent maintenance overhead.

Each level in the logical hierarchy MUST separated with a backslash (`\`) to
be familiar to Namespaces. A hierarchy MAY be of endless depth but it is
RECOMMENDED to keep the depth at less or equal than six levels.

Please note that the @package applies to different "Structural Elements"
depending where it is defined.

1. If the @package is defined in the *file-level* DocBlock then it only applies
   to the following elements in the applicable file:

   * global functions
   * global constants
   * global variables
   * requires and includes

2. If the @package is defined in a *namespace-level* or *class-level* DocBlock
   then the package applies to that namespace, class or interface and their
   contained elements.
   This means that a function which is contained in a namespace with the
   @package tag assumes that package.

This tag MUST NOT occur more than once in a "DocBlock".

#### Examples

```php
/**
 * @package PSR\Documentation\API
 */
```

### 7.13. @param

### 7.14. @property

### 7.15. @return

The @return tag is used to document the return value of functions or methods.

#### Syntax

    @return <"Type"> [description]

#### Description

With the @return tag it is possible to document the return type and function of a
function or method. When provided it MUST contain a "Type" (See Appendix A)
to indicate what is returned; the description on the other hand is OPTIONAL yet
RECOMMENDED in case of complicated return structures, such as associative arrays.

The @return tag MAY have a multi-line description and does not need explicit
delimiting.

It is RECOMMENDED when documenting to use this tag with every function and
method. Exceptions to this recommendation are:

1. **constructors**, the @return tag MAY be omitted here, in which case an
   interpreter MUST interpret this as if `@return self` is provided.
2. **functions and methods without a `return` value**, the @return tag MAY be
   omitted here, in which case an interpreter MUST interpret this as if
   `@return void` is provided.

This tag MUST NOT occur more than once in a "DocBlock" and is limited to the
"DocBlock" of a "Structural Element" or type method or function.

#### Examples

```php
/**
 * @return integer Indicates the number of items.
 */
function count()
{
    <...>
}
```

```php
/**
 * @return string|null The label's text or null if none provided.
 */
function getLabel()
{
    <...>
}
```

### 7.16. @see

### 7.17. @since

#### Description

It is NOT RECOMMENDED for this tag to occur more than once in a "DocBlock".

### 7.18. @subpackage [deprecated]

This tag MUST NOT occur more than once in a "DocBlock".

### 7.19. @throws

### 7.20. @todo

### 7.21. @type

You may use the @type tag to document the "Type" of the following
"Structural Elements":

* Constants, both class and global
* Properties
* Variables, both global and local scope

#### Syntax

    @type <"Type"> [description]

#### Description

The @type tag is the successor of the @var tag and serves the purpose of defining
which type of data is contained in a Constant, Property or Variable.

Each Constant or Property *definition* MUST be preceded by a DocBlock
containing the @type tag. Each Variable, where the type is ambiguous or unknown,
SHOULD be preceded by a DocBlock containing the @type tag. Any other
variable MAY be preceeded with a similar DocBlock.

It is NOT RECOMMENDED to use the @var alias unless it is necessary in order for
the application, or associated tools, to function correctly.

This tag MUST NOT occur more than once in a "DocBlock".

#### Examples

```php
/** @type int This is a counter. */
$int = 0;

// there should be no docblock here
$int++;
```

Or:

```php
class Foo
{
  /** @type string|null Should contain a description if available */
  protected $description = null;

  public function setDescription($description)
  {
      // there should be no docblock here
      $this->description = $description;
  }
}
```

Another example is to document the variable in a foreach explicitly; many IDEs
use this information to help you with auto-completion:

```php
/** @type \Sqlite3 $sqlite */
foreach($connections as $sqlite) {
    // there should be no docblock here
    $sqlite->open('/my/database/path');
    <...>
}
```

### 7.22. @uses

### 7.23. @var [deprecated]

Is a **deprecated** alias for `@type`, please see the documentation for `@type`
for details of its usage.

This tag MUST NOT occur more than once in a "DocBlock".

### 7.24. @version

## Appendix A. Types

### ABNF

    type-expression          = 1*(array-of-type-expression|array-of-type|type ["|"])
    array-of-type-expression = "(" type-expression ")[]"
    array-of-type            = type "[]"
    type                     = class-name|keyword
    class-name               = 1*CHAR
    keyword                  = "string"|"integer"|"int"|"boolean"|"bool"|"float"
                               |"double"|"object"|"mixed"|"array"|"resource"
                               |"void"|"null"|"callback"|"false"|"true"|"self"

### Additional details

When a "Type" is used the user will expect a value, or set of values, as
detailed below.

When the "Type" may consist of multiple types then these MUST be separated
with the vertical bar sign (|). Any application supporting this specification MUST
recognize this and split the "Type" before processing.

For example: `@return int|null`

The value represented by "Type" can be an array. The type MUST be defined
following the format of one of the following options:

1. unspecified, no definition of the contents of the represented array is given.
   Example: `@return array`

2. specified containing a single type, the Type definition informs the reader of
   the type of each array element. Only one type is then expected as element for
   a given array.

   Example: `@return int[]`

   Please note that _mixed_ is also a single type and with this keyword it is
   possible to indicate that each array element contains any possible type.

3. specified containing multiple types, the Type definition informs the reader
   of the type of each array element. Each element can be of any of the given
   types.
   Example: `@return (int|string)[]`

The supported atomic types are either a *valid class name* or *keyword*.

### Valid Class Name

A valid class name seen from the context where this type is mentioned. Thus
this may be either a Fully Qualified Class Name (FQCN) or if present in a
namespace a local name.

> It is RECOMMENDED for applications to expand any local name into a FQCN
> for easier processing and comparisons.

The element to which this type applies is either an instance of this class
or an instance of a class that is a (sub-)child to the given class.

> Due to the above nature it is RECOMMENDED for applications that
> collect and shape this information to show a list of child classes
> with each representation of the class. This would make it obvious
> for the user which classes are acceptable as type.

### Keyword

A keyword defining the purpose of this type. Not every element is determined
by a class but still worth of a classification to assist the developer in
understanding the code covered by the DocBlock.

> Note: most of these keywords are allowed as class names in PHP and as
> such are hard to distinguish from real classes. As such the keywords MUST
> be lowercase, as most class names start with an uppercase first character,
> and you SHOULD NOT use classes with these names in your code.

> There are more reasons to not name classes with the names of these
> keywords but that falls beyond the scope of this specification.

The following keywords are recognized by this PSR:

1.  'string', the element to which this type applies is a string of
    binary characters.

2.  'integer' or 'int', the element to which this type applies is a whole
    number or integer.

3.  'boolean' or 'bool', the element to which this type applies only has
    state true or false.

4.  'float' or 'double', the element to which this type applies is a continuous,
    or real, number.

5.  'object', the element to which this type applies is the instance of an
    undetermined class.
    This could be considered an alias for providing the class stdClass, as this
    is the base class of all classes, but the intention of the type differs.

    Providing stdClass will imply the intention that the related element contains
    an actual object of class stdClass or direct descendant, whereas object
    implies that it is completely unknown of which class the contained
    object will be.

6.  'mixed', the element to which this type applies can be of any type as
    specified here. It is not known on compile time which type will be used.

7.  'array', the element to which this type applies is an array of values.

8.  'resource', the element to which this type applies is a resource per
    the definition of PHP at
    http://www.php.net/manual/en/language.types.resource.php.

9.  'void', this type is commonly only used when defining the return type of a
    method or function.
    The basic definition is that the element indicated with this type does not
    contain a value and the user should not rely on any retrieved value.

    For example:

    ```php
    /**
     * @return void
     */
    function outputHello()
    {
        echo 'Hello world';
    }
    ***

    In the example above no return statement is specified and thus is the return
    value not determined.

    Example 2:

    ```php
    /**
     * @param boolean $hi when true 'Hello world' is echo-ed.
     *
     * @return void
     */
    function outputHello($quiet)
    {
        if ($quiet} {
            return;
        }
        echo 'Hello world';
    }
    ```

    In this example the function contains a return statement without a given
    value. Because there is no actual value specified does this also constitute
    as type 'void'.

10. 'null', the element to which this type applies is a NULL value or, in
    technical terms, does not exist.

    A big difference compared to void is that this type is used in any situation
    where the described element may at any given time contain an explicit NULL
    value.

    Example:

    ```php
    /**
     * @return null
     */
    function foo()
    {
        echo 'Hello world';
        return null;
    }
    ```

    This type is commonly used in conjunction with another type to indicate that
    it is possible that nothing is returned.

    Example:

    ```
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
    ```


11. 'callback', the element to which this type applies is a pointer to a
    function call. This may be any type of callback as defined in the PHP manual
    at http://php.net/manual/en/language.pseudo-types.php.

12. 'false' or 'true', the element to which this type applies will have
    the value true or false. No other value will be returned from this
    element.

13. 'self', the element to which this type applies is of the same Class,
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

    > Due to the above nature it is RECOMMENDED for applications that
    > collect and shape this information to show a list of child classes
    > with each representation of the class. This would make it obvious
    > for the user which classes are acceptable as type.

## Appendix B. Differences Compared With The De-facto PHPDoc Standard

This specification intends to improve upon the de-facto PHPDoc standard by
expanding syntax and deprecating redundant elements.

The following changes may be observed and a concise rationale is provided.

### Syntax Changes

#### Added Support For Namespaces

#### Added 'self' As "Type"

#### Added Array Notation For "Type"

#### Expanded Basic Syntax To Support Doctrine2/Symfony2 Style Annotations

### Tag Additions

#### @api

Framework and library developers needed a way for consumers to distinguish
between public methods intended for internal use or public methods fit for
consuming.

#### @type

This tag is meant to replace @var and better convey the meaning and intention:
to tell the user which *type* of data is contained in the succeeding
"Structural Element".

Additionally constants did not have a tag which identified the type of the
contained data. Instead of re-using @var, which is inappropriate since
a constant is not variable, or defining a *new* tag @const, which would make
usage of the right tag even more complex, a new tag @type was created to replace
@var and serve as new tag for constants.

### Tag Changes

#### @package

The @package tag replaces the function of the @category and @subpackage by
allowing a layered hierarchy using the '\' operator.

See the documentation on the @package tag for details.

#### @ignore

The @ignore tag has not been included in this specification as it is not a tag
but an annotation specific to phpDocumentor and derived documentation
generators.

#### @link

In the de-facto standard, introduced by phpDocumentor, @link supported the use
of internal locators for "Structural Element". This use is actually superceded
by the @see tag and thus removed.

It is NOT RECOMMENDED to use the @link tag for referencing "Structural Elements".

#### @method

phpDocumentor had a different signature where the method name was repeated. Since
this repetition only caused overhead it has been removed in this specification.

### Deprecated Tags

#### @category

The @package tag replaces the function of the @category and @subpackage by
allowing a layered hierarchy using the '\' operator.

See the documentation on the @package tag for details.

#### @subpackage

The @package tag replaces the function of the @category and @subpackage by
allowing a layered hierarchy using the '\' operator.

See the documentation on the @package tag for details.

#### @var

The @var tag is considered ambiguous and non-semantic; as such it is superceded
by the @type tag which also allows the type of constants to be documented.