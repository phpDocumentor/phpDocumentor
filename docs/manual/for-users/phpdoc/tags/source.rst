@source
========

.. important::

   The effects of this tag are not yet fully implemented in PhpDocumentor2.

The @source tag shows the source code of :term:`Structural Elements`.

Syntax
------

    @source [<start-line> [<number-of-lines>] ] [<description>]

Description
-----------

The @source tag can be used to communicate the implementation of
:term:`Structural Elements` by presenting their source code, or more typically -
portions of it.

If specified, the starting line MUST be a positive integer. Counting starts at
the line where the Structural Element's body started (i.e. the line with the
opening brace).

This line number MAY optionally be followed by another positive integer,
specifying the number of lines to present from the starting line onwards. If
omitted, the Structural Element's source is presented from the starting line, to
its end (i.e. the line with the closing brace).

If no starting line is specified, the Structural Element's source is presented
in its entirety.

It is RECOMMENDED (but not required) to provide an additional description to give
a more detailed explanation about the presented code.

Examples
--------

.. code-block:: php
   :linenos:

    /**
     * @source 2 1 Check that ensures lazy counting.
     */
    function count()
    {
        if (null === $this->count) {
            <...>
        }
    }