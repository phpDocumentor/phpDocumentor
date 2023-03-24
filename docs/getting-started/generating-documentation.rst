Generating documentation
========================

After you have :ref:`installed<installing>` phpDocumentor you can use the ``phpdoc`` command to generate
your documentation.

Throughout this documentation we expect that the ``phpdoc`` command is available; thus whenever we ask you
to run a command, it will be in the following form

.. code-block:: shell-session

    $ phpdoc

When you have installed a version via composer or manually you should invoke the ``phpdoc`` script in
the ``bin`` folder of your phpDocumentor installation.

Under Linux / MacOSX that would be

.. code-block:: shell-session

    $ [PHPDOC_FOLDER]/bin/phpdoc

And under Windows that would be

.. code-block:: shell-session

    $ [PHPDOC_FOLDER]\bin\phpdoc.bat

The basic usage of phpDocumentor is to provide an input location using the command line options
(``-d`` for a directory, ``-f`` for a file) and tell it to output your documentation to a folder of your
liking (``-t``).

For example:

.. code-block:: shell-session

    $ phpdoc -d ./src -t ./docs/api

What the above example does, is scan all files in the ``src`` directory and its subdirectories, perform
an analysis and generate a website containing the documentation in the folder ``docs/api``. If you want,
you can omit the ``-t`` option, in which case the output will be written to a subfolder called ``output``.

Read more
~~~~~~~~~

- :doc:`../references/configuration`, there are a lot more options to phpDocumentor. To maintain consistent
  documentation, it is good practice to define them all in a :doc:`../references/configuration` file and to
  include that in your project.
- :doc:`../guides/running-phpdocumentor`, there are a ton more options you can add onto the command line; check
  this out!
