Anatomy of a DocBlock
=====================

Basics
------


Sections
--------

A DocBlock roughly exists of 3 sections:

1. Short Description; a one-liner which globally states the function of the documented element.
2. Long Description; an extended description of the function of the documented element; may contain markup and inline tags.
3. Tags; a series of descriptors for properties of this element; such as @param and @return.

### Short Description

### Long Description

### Tags

Inheritance
-----------

Docblocks automatically inherits the Short and Long description of an
overridden, extended or implemented element.

For example: if Class B extends Class A and it has an empty DocBlock defined,
then it will have the same Short description and Long description as Class A.
No DocBlock means that the 'parent' DocBlock will not be overridden and an error
will be thrown during parsing.

This form of inheritance applies to any element that can be overridden, such as
Classes, Interfaces, Methods and Properties. Constants and Functions can not be
overridden in and thus do not have this behavior.

Please note that you can also augment a Long Description with its parent's
Long Description using the {@inheritdoc} inline tag.

Each element also inherits a specific set of tags; which ones depend on the type
of element.

The following applies:

  * *ALL*, @author, @version, @copyright
  * *Classes*, @category, @package, @subpackage
  * *Methods*, @param, @return, @throws, @throw

List of Inline Tags
-------------------

Please note that the behavior of tags with headers suffixed with an asterisk is
not yet implemented.

### @example*
### @id*
### @internal*
### @inheritdoc*
### @link*
### @source*
### @toc*
### @tutorial*

List of Tags
------------

Please note that the behavior of tags with headers suffixed with an asterisk is
not yet implemented; the tag and any contents are however visible in the
documentation.

### @abstract*
### @access
### @api
### @author*
### @category*
### @copyright
### @deprecated*
### @example*
### @final*
### @filesource*
### @global*
### @ignore*
### @internal*
### @license
### @link
### @method*
### @name*
### @package
### @param
### @property*
### @return
### @see*
### @since*
### @static*
### @staticvar*
### @subpackage
### @throws / @throw
### @todo
### @tutorial*
### @uses / @usedby*
### @var
### @version*