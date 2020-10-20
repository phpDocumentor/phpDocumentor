Your First Set of Documentation
===============================

Overview
--------

The goal of this tutorial is to introduce you to writing and subsequently generating effective documentation
with phpDocumentor.

Writing a DocBlock
------------------

A DocBlock is a piece of *documentation* in your source code that informs you what the function of
a certain class, method or other *Structural Element* is.

Which elements can be documented?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Before we discuss what a DocBlock looks like, let's first zoom in on what you can document with them.
PhpDocumentor follows the PHPDoc definition and recognizes the following *Structural Elements*:

* Function_
* Constant_
* Class_
* Interface_
* Trait_
* `Class constant`_
* Property_
* Method_

In addition to the above, the PHPDoc standard also supports DocBlocks for *Files* and ``include``/``require``
statements, even though PHP itself does not know this concept.

Each of these elements can have exactly one DocBlock associated with it, which directly precedes it.
No code or comments may be between a DocBlock and the start of an element's definition.

What does a DocBlock look like?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

DocBlocks are always enclosed in a particular comment-type, called *DocComment*. A DocComment starts
with ``/**`` (opener) and ends with ``*/`` (closer). Each line in between the DocComment opener and
its closer should start with an asterisk (``*``). Every DocBlock precedes exactly one *Structural Element*
and all contents of the DocBlock apply to that associated element.

For example:

.. code-block:: php

   <?php
   /**
    * This is a DocBlock.
    */
   function associatedFunction()
   {
   }

.. note::

   **File-level DocBlocks**

   Quite often projects will also want to document the license or role for an entire file.
   This can be accomplished by having a DocBlock as the first element encountered in a file.
   It is important to note that whenever another *Structural Element* directly follows the DocBlock,
   it is no longer recognized as a File-level DocBlock, but as belonging to the subsequent element.

   The following DocBlock is a File-level DocBlock:

   .. code-block:: php

      <?php
      /**
       * I belong to a file
       */

      /**
       * I belong to a class
       */
      class Def
      {
      }

   In contrast, in the following example the DocBlock belongs to the class:

   .. code-block:: php

      <?php
      /**
       * I belong to a class
       */

      class Def
      {
      }

DocBlocks are divided into the following three parts. Each of these parts is optional, except that a Description
may not exist without a Summary.

Summary
  The Summary, sometimes called a short description, provides a brief introduction into the function
  of the associated element. A Summary ends when it encounters either of the below situations::

    * a period ``.``, followed by a line break
    * or a blank (comment) line.

Description
  The Description, sometimes called the long description, can provide more information. Examples of additional
  information are: a description of a function's algorithm, a usage example or a description of how a class
  fits in the whole of the application's architecture. The description ends when the first tag is encountered
  on a new line or when the DocBlock is closed.

Tags and Annotations
  These provide a way to succinctly and uniformly provide meta-information about the associated element.
  Tags can, for example, describe the type of information that is returned by a method or function.
  Each tag is preceded by an at-sign (``@``) and starts on a new line.

Example
~~~~~~~

A DocBlock looks like this:

.. code-block:: php
   :linenos:

    <?php
    /**
     * A summary informing the user what the associated element does.
     *
     * A *description*, that can span multiple lines, to go _in-depth_ into
     * the details of this element and to provide some background information
     * or textual references.
     *
     * @param string $myArgument With a *description* of this argument,
     *                           these may also span multiple lines.
     *
     * @return void
     */
     function myFunction($myArgument)
     {
     }

Let's go through this example line by line and discuss which is which,

Line 2
  shows that a DocBlock starts with the opening sequence ``/**``.

Line 3
  has an example of a Summary. This is, usually, a single line but may cover multiple lines as long as the end
  of the summary, as defined in the previous chapter, is not reached.

Line 5, 6 and 7
  show an example of a Description, which may span multiple lines and can be formatted using the
  Markdown_ markup language. Using Markdown_ you can make text **bold**, *italic*, add numbered lists
  and even provide ``code`` examples.

Line 9 and 12
  show that you can include :doc:`tags<../references/phpdoc/tags/index>` in your DocBlocks to provide
  additional information about the succeeding element.
  In this example, we declare that the argument ``$myArgument`` is of type ``string``, with a description
  what this argument represents, and we declare that the return value for this method is ``void``, which
  means that no value will be returned.

Line 13
  shows the closing sequence ``*/``, which is the same as that for a multi-line comment (``/* .. */``).

If you'd like to know more about what DocBlocks do for you, visit the chapter :doc:`../guides/docblocks`
for more in-depth information.

Running phpDocumentor
---------------------

After you have :doc:`installed<installing>` phpDocumentor you can use the ``phpdoc`` command to generate
your documentation.

Throughout this documentation we expect that the ``phpdoc`` command is available; thus whenever we ask you
to run a command, it will be in the following form::

    $ phpdoc

.. hint::

    When you have installed a version via composer or manually you should invoke the ``phpdoc`` script in
    the ``bin`` folder of your phpDocumentor installation.

    Under Linux / MacOSX that would be::

        $ [PHPDOC_FOLDER]/bin/phpdoc

    And under Windows that would be::

        $ [PHPDOC_FOLDER]\bin\phpdoc.bat

The basic usage of phpDocumentor is to provide an input location using the command line options
(``-d`` for a directory, ``-f`` for a file) and tell it to output your documentation to a folder of your
liking (``-t``).

For example::

    $ phpdoc -d ./src -t ./docs/api

What the above example does, is scan all files in the ``src`` directory and its subdirectories, perform
an analysis and generate a website containing the documentation in the folder ``docs/api``. If you want,
you can omit the ``-t`` option, in which case the output will be written to a subfolder called ``output``.

Read more
~~~~~~~~~

PhpDocumentor features several templates_ with which you can change the appearance of your documentation.
See the chapter :doc:`changing-the-look-and-feel` for more information on how to switch between templates.

There are a lot more options to phpDocumentor. To maintain consistent documentation, it is good practice
to define them all in a :doc:`../references/configuration` file and to include that in your project.

If you'd like to know more on running phpDocumentor; see the guide on :doc:`../guides/running-phpdocumentor`
for more information.

.. _Function:       https://www.php.net/language.functions
.. _Constant:       https://www.php.net/language.constants
.. _Class:          https://www.php.net/language.oop5.basic
.. _Interface:      https://www.php.net/language.oop5.interfaces
.. _Trait:          https://www.php.net/language.oop5.traits
.. _Class constant: https://www.php.net/language.oop5.constants
.. _Property:       https://www.php.net/language.oop5.properties
.. _Method:         https://www.php.net/language.oop5.basic
.. _Markdown:       https://daringfireball.net/
.. _templates:      https://phpdoc.org/templates
