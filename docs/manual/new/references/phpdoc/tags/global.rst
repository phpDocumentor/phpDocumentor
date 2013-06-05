@global
=======

.. important::

   This tag is not included in phpDocumentor 2.0 and may be included in a
   subsequent version.

.. note::

   This definition is not yet official for phpDocumentor2; slight changes may
   be made to this definition to match new standards. These changes will however
   remain compatible with this definition.

.. warning:: The use of globals is discouraged

The @global tag is used to inform phpDocumentor of a global variable _or_ its
usage.

Syntax
------

    @global [:term:`Type`] [name]
    @global [:term:`Type`] [description]

Description
-----------

Since there is no standard way to declare global variables, a @global tag MAY
be used in a DocBlock preceding a global variable's definition. To support
previous usage of @global, there is an alternate syntax that applies to
DocBlocks preceding a function, used to document usage of global
variables. in other words, There are two usages of @global: definition and
function usage.

Definition
~~~~~~~~~~

The parser WILL NOT attempt to automatically parse out any global variables and
only one @global tag MAY be allowed per global variable DocBlock. A global
variable DocBlock MUST be followed by the global variable's definition before
any other element or DocBlock occurs in the source.

The name MUST be the exact name of the global variable as it is declared in
the source (though @name MAY be used to change the name displayed by
documentation).

Function usage
~~~~~~~~~~~~~~

The function/method @global syntax MAY be used to document usage of global
variables in a function, and MUST NOT have a $ starting the third word. The
:term:`Type` will be ignored if a match is made between the declared global
variable and a variable documented in the project.

A parser SHOULD display the optional description unmodified.

Examples
--------

.. note::

   Examples for this tag should be added