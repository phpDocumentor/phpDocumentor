@internal
=========

The ``@internal`` tag is used to denote that associated *Structural Elements*
are internal to the application or library. It may also be used inside
a long description to insert a piece of text that is only applicable for
the developers of the software.

Syntax
------

.. code-block::

    @internal [description]

or inline:

.. code-block::

    {@internal [description]}

Contrary to other inline tags, the inline version of this tag may contain
other inline tags.

Description
-----------

The ``@internal`` tag indicates that the associated *Structural Element* is intended
only for use within the application, library or package to which it belongs.

Authors MAY use this tag to indicate that an element with public visibility should
be regarded as exempt from the API - for example:
  * Library authors MAY regard breaking changes to internal elements as being exempt
    from semantic versioning.
  * Static analysis tools MAY indicate the use of internal elements from another
    library/package with a warning or notice.

An additional use of ``@internal`` is to add internal comments or additional
description text inline to the Description. This may be done, for example,
to withhold certain business-critical or confusing information, when generating
documentation from the source code of this piece of software.

    It is NOT RECOMMENDED to store passwords or security sensitive information
    in your DocBlock. Not even with this tag.

Effects in phpDocumentor
------------------------

*Structural Elements*, or parts of the long description when the tag is
used inline, tagged with the ``@internal`` tag will be filtered out when creating
the HTML output unless the ``--parseprivate`` command line argument is used.

The Abstract Syntax Tree will still contain the internal information.
Any consumer of this file is responsible for filtering the information correctly.

Examples
--------

Normal tag:: Mark the ``count()`` function as being internal to this project:

.. code-block:: php
   :linenos:

    /**
     * @internal
     *
     * @return int Indicates the number of items.
     */
    function count()
    {
        <...>
    }

Inline tag:: Include a note in the Description that would only show in Developer Docs:

.. code-block:: php
   :linenos:

    /**
     * Counts the number of Foo.
     *
     * This method gets a count of the Foo.
     * {@internal Developers should note that it silently
     *            adds one extra Foo (see {@link http://example.com}).}
     *
     * @return int Indicates the number of items.
     */
    function count()
    {
        <...>
    }
