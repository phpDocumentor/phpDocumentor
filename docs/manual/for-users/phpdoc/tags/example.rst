@example
========

.. important::

   The effects of this tag are not yet fully implemented in PhpDocumentor2.

The @example tag shows the code of a specified example file, or optionally, just
a portion of it.

Syntax
------

    @example [location] [<start-line> [<number-of-lines>] ] [<description>]

or inline

    {@example [location] [<start-line> [<number-of-lines>] ] [<description>]}

Description
-----------

The @example tag can be used to demonstrate the use of :term:`Structural Elements`
by presenting the contents of files that use them.

A location to a file MUST be specified. It can be specified as a relative or
absolute URI, or as a relative or absolute file path. Use double quotes around
the location to explicitly specify that it is a file path.

To expand a relative URI or filepath, PhpDocumentor looks into multiple folders,
until it finds an existing and readable file matching the one specified. The
folders being analyzed (in this order) are:
-  A specific folder, specified at the configuration file or command line.
-  The source file's folder.
-  A folder called "examples", relative to the project root.

If specified, the starting line MUST be a positive integer. It specifies a line
number from which to display the example.

This line number MAY optionally be followed by another positive integer,
specifying the number of lines to present from the starting line onwards. If
omitted, the example file is presented from the starting line, to the end of the
file.

If no starting line is specified, the example file is presented in its entirety.

It is RECOMMENDED (but not required) to provide an additional description to give
a more detailed explanation about the presented code.

Examples
--------

.. code-block:: php
   :linenos:

    /**
     * @example example1.php Counting in action.
     * @example http://example.com/example2.phps Counting in action by a 3rd party.
     * @example "My Own Example.php" My counting.
     */
    function count()
    {
        <...>
    }