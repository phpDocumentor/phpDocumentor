Your First Set of Documentation
===============================

Overview
--------

The goal of this tutorial is to introduce you in writing and subsequently generating effective documentation with
phpDocumentor.

Writing a DocBlock
------------------

A DocBlock is a piece of inline *documentation* in your source code that informs you what a class, method or other
:term:`Structural Element` its function is.

Which elements can be documented?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Before we discuss what a DocBlock looks like, let's first zoom into what you can document with them. phpDocumentor
follows the PHPDoc definition and recognizes the following :term:`Structural Elements`:

* Function_
* Constant_
* Class_
* Interface_
* Trait_
* `Class constant`_
* Property_
* Method_

In addition to the above the PHPDoc standard also supports :term:`DocBlocks` for *Files* and include/require statements,
even though PHP itself does not know this concept.

Each of these elements can have exactly one DocBlock associated with it, which directly precedes it. No code or
comments may be between a DocBlock and the start of an element's definition.

What does a DocBlock look like?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

:term:`DocBlocks` are always enclosed in a comment-type, called :term:`DocComment`, that starts with ``/**`` and ends
with ``*/``. Each line in between the opening and closing statement should start with an asterisk (``*``). Every
:term:`DocBlock` precedes exactly one :term:`Structural Element` and all contents of the :term:`DocBlock` apply to that
associated element.

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

   Quite often projects will want to document the license or function for an entire file instead of a single element.
   This can be accomplished by having a DocBlock as the first element encountered in a file. It is important to note that
   whenever another :term:`Structural Element` directly follows the DocBlock that it is no longer recognized as a
   File-level DocBlock but belonging to the subsequent element.

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

   However in the following example the DocBlock belongs to the class:

   .. code-block:: php

      <?php
      /**
       * I belong to a class
       */

      class Def
      {
      }

DocBlocks are divided into the following three parts. Each of these parts is optional, except that a :term:`Description`
may not exist without a :term:`Summary`.

:term:`Summary`
  Sometimes called a short description, provides a brief introduction into the function of the associated element.
  A Summary ends
  in one of these situations:

    1. A dot is following by a line break, or
    2. Two subsequent line breaks are encountered.

:term:`Description`
  Sometimes called the long description, can provide more information. Examples of additional information is a
  description of a function's algorithm, a usage example or description how a class fits in the whole of the
  application's architecture. The description ends when the first tag is encountered or when the DocBlock is closed.

:term:`Tags` and :term:`annotations`
  These provide a way to succinctly and uniformly provide meta-information about the associated element. This could,
  for example, describe the type of information that is returned by a method or function. Each tag is preceded by an
  at-sign (`@`) and starts on a new line.

Example
~~~~~~~

A DocBlock looks like this:

.. code-block:: php
   :linenos:

    <?php
    /**
     * A summary informing the user what the associated element does.
     *
     * A *description*, that can span multiple lines, to go _in-depth_ into the details of this element
     * and to provide some background information or textual references.
     *
     * @param string $myArgument With a *description* of this argument, these may also
     *    span multiple lines.
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
  has an example of a :term:`Summary`. This is, usually, a single line but may cover multiple lines as long as the end
  of the summary, as defined in the previous chapter, is not reached.

Line 5 and 6
  show an example of a :term:`Description`, which may span multiple lines and can be formatted using the
  Markdown_ markup language. Using Markdown_ you can make text bold, italic, add numbered lists and even provide code
  examples.

Line 8 and 11
  show that you can include :doc:`tags<../references/phpdoc/tags/index>` with your DocBlocks to provide additional
  information about the succeeding element.
  In this example we declare that the argument ``$myArgument`` is of type string, with a description what this argument
  represents, and we declare that the return value for this method is void, which means that there is no value returned.

Line 12
  shows the closing statement ``*/``, which is the same as that for a multiline comment (``/* .. */``).

If you'd like to know more about what DocBlocks do for you, visit the chapter :doc:`../guides/docblocks` for more
in-depth information.

Running phpDocumentor
---------------------

After you have :doc:`installed <installing>` phpDocumentor you can use the ``phpdoc`` command to generate
your documentation.

In this document we expect that the phpdoc command is available; thus whenever we ask you to run a command
it would be in the following form::

    $ phpdoc

.. hint::

    When you have installed a version via composer or manually you should invoke the ``phpdoc.php`` script in
    the ``bin`` folder of your phpDocumentor installation.

    Under Linux / MacOSX that would be::

        $ [PHPDOC_FOLDER]/bin/phpdoc.php

    And under Windows that would be::

        $ [PHPDOC_FOLDER]\bin\phpdoc.bat

The basic usage of phpDocumentor is to provide an input location using the command line options
(``-d`` for a directory, ``-f`` for a file) and tell it to output your documentation to a folder of your
liking (``-t``).

For example::

    $ phpdoc -d ./src -t ./docs/api

What the above example does is scan all files in the ``src`` directory and its subdirectories, perform an analysis and
generate a website containing the documentation in the folder ``docs/api``. If you want you can even omit the ``-t``
option, in which case the output will be written to a subfolder called ``output``.

.. hint::

   phpDocumentor features several templates_ with which you can change the appearance of your documentation. See the
   chapter :doc:`changing-the-look-and-feel` for more information on how to switch between templates.

There are a lot more options to phpDocumentor and you can define them all in a :doc:`../references/configuration` file
and include that in your project but that is out of scope for this tutorial. If you'd like to know more on running
phpDocumentor; see the guide on :doc:`../guides/running-phpdocumentor` for more information.

.. _Function:       http://php.net/manual/en/language.functions.php
.. _Constant:       http://php.net/manual/en/language.constants.php
.. _Class:          http://php.net/manual/en/language.oop5.basic.php
.. _Interface:      http://php.net/manual/en/language.oop5.interfaces.php
.. _Trait:          http://php.net/manual/en/language.oop5.traits.php
.. _Class constant: http://php.net/manual/en/language.oop5.constants.php
.. _Property:       http://php.net/manual/en/language.oop5.properties.php
.. _Method:         http://php.net/manual/en/language.oop5.basic.php
.. _Markdown:       http://daringfireball.com
.. _templates:      http://phpdoc.org/templates
