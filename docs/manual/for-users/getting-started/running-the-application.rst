Running the application
=======================

You can use the ``phpdoc`` command to generate your documentation
for you.

In this document is shown how phpDocumentor can be used to generate your
documentation. It is expected that the phpdoc command is available; thus
whenever we ask you to run a command it would be in the following form::

    $ phpdoc

When you have installed a version via the installer you should invoke the
``phpdoc.php`` script in the ``bin`` folder of your phpDocumentor installation
unless you have added a symlink as described in the chapter :doc:`../installation`.

Under Linux / MacOSX that would be::

    $ [PHPDOC_FOLDER]/bin/phpdoc.php

And under Windows that would be::

    $ [PHPDOC_FOLDER]\bin\phpdoc.bat

Introduction
------------

phpDocumentor takes a two-step approach to generating documentation:

1. Parse the source files and create a file containing the structure of your
   project (called an Abstract Syntax Tree, or AST; see the *structure.xml* file
   after execution).
2. Transform the Abstract Syntax Tree (AST) to a form of human readable output,
   such as HTML.

These steps can be executed at once or separately, depending on your preference.

Generating documentation
------------------------

To generate your documentation you can invoke phpDocumentor without specifying
a command::

    $ phpdoc

When ran without parameters (as shown above) it will try to get the location of
the source code and the target folder from a configuration file (which is
discussed in the :doc:`../configuration` chapter) or exit with an error.

You can use the help option (``-h`` or ``--help``) to view a list of all
possible options.

.. code-block:: bash

    $ phpdoc -h

The simplest action would be to invoke phpDocumentor to parse a given
location (``-d`` for a directory, ``-f`` for a file) and tell it to
output your documentation to a given target folder (``-t``) using
the following command::

    $ phpdoc -d [SOURCE_PATH] -t [TARGET_PATH]

Please be aware that phpDocumentor expects the target location to either exist
or that it can be created with the current user. If it is netiher, the
application will exit and tell you why.

Commands
--------

Usage
~~~~~

phpDocumentor has a command oriented CLI; the first argument represents the name
of the command to execute, if no name is given then phpDocumentor assumes you
want to run the ``project:run`` command. This last mechanism provides backwards
compatibility with phpDocumentor 1.x.

Example::

    $ phpdoc -d . -t output

Would result in the ``project:run`` command being executed with parameter
``-d`` and ``-t``.

Another example::

    $ phpdoc run -d . -t output

would have the same effect as the previous command; if no 'namespace'
(thus _project:_) is provided then the namespace ``project`` is assumed.

Last example::

   $ phpdoc project:run -d . -t output

would have the same effect as the previous examples and is the most explicit
form.

phpDocumentor contains a whole series of commands that are described in the
appendix :doc:`../command-reference`.