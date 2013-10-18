Your First Set of Documentation
===============================

Overview
--------

The goal of this tutorial is to introduce you in writing and subsequently generating effective documentation with
phpDocumentor.

Writing a DocBlock
------------------

A DocBlock is a piece of *documentation* in your source code that informs you what a :term:`Structural Element` does to
which it is associated. PHP, and thus phpDocumentor, recognizes the following structural elements:

* Function_
* Constant_
* Class_
* Interface_
* Trait_
* `Class constant`_
* Property_
* Method_

In addition to the above phpDocumentor also supports DocBlocks for *Files*, even though PHP does not officially
support it.

A DocBlock looks like this:

.. code-block:: php
   :linenos:

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

As you can see on line 1, a DocBlock starts with the opening sequence ``/**``. This will tell PHP that a DocBlock is
defined here. Line 10 shows the closing statement ``*/``,which is the same as that for a multiline comment (`/* .. */`).

On line 2 you see an example of a summary, this is, usually, single line but can cover multiple lines. The summary will
end as soon as a dot and newline is encountered or when there are two newlines behind a sentence.

Line 4 and 5 show an example of a description, which may span multiple lines and can be formatted using the Markdown_
markup language. Using Markdown_ you can make text bold, italic, add numbered lists and even code examples.

And last but definitely not least line 7 and 9 show that you can include :doc:`tags<../references/phpdoc/tags/index>`
with your DocBlocks to provide additional information about the succeeding element. In this example we declare that the
argument ``$myArgument`` is of type string and has some additional information, and we declare that the return value
for this method is void, which means as much as that there is no value returned.

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

   phpDocumentor features several templates_

There are a lot more options to phpDocumentor and you can define them all in a :doc:`../references/configuration` file
and include that in your project but that is out of scope for this tutorial. If you'd like to know more on running
phpDocumentor; see the chapter on :doc:`../guides/running-phpdocumentor` for more information.

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
