Table of Contents
=================

Scrybe generates a Table of Contents for every file by scanning for headings and
includes (such as the ``toctree`` directive).

Every time a file named ``index`` (with the appropriate extension) is detected
a new ``module`` is created and populated with entries.

.. note::

   The concept behind using index files is that you can create modules at will
   or include the documentation of another project or library and it will show
   up in a modules listing.

A TableOfContents tree is built using the instructions provided by the
``toctree`` directive, where the root of the tree is at the ``index`` file.
This means that a file that is not mentioned in a ``toctree`` directive is never
included in the Table of Contents. *It is however generated and can be linked to
using hyperlinks.*

.. figure:: images/TableOfContents_Class_Diagram.png
            Class diagram

Breadcrumb
----------

The Table Of Contents can then be used to re-create a breadcrumb which may be
displayed on each page.
In general the breadcrumb follows the convention:

    Homepage indicator > Module > Chapter > Subchapter

To correctly implement a breadcrumb does every entry of the Table of Contents
support a ``getParent()``, ``getNext()`` and ``getPrevious()`` method that
contains a pointer to another ``File``.

Detection algorithm
-------------------

Constructing the Table of Contents happens in two phases:

1. Collecting files and headings.
2. Constructing a tree per module.

During the collections phase does Scrybe gather a listing of all individual
files in sequential order and a hierarchical listing of headings and files that
point to the sequential file array.

This is a complete, yet unordered and unfiltered, table of contents.

.. note::

   if we were to use this alone as basis for the table of contents then the
   display order would be as discovered on the file system and would it be hard
   for the user to influence the order of presentation.

During the construction phase will Scrybe find all ``File``s named 'index' and
ending in the selected input extension (thus: index.rst for RestructuredText)
and create a module object with that ``File`` as root.

By appointing the 'index' file as root for a given module can the template
iterate through the index and its children and as such discover the flow that
connects the ``toctree`` directives.

This means that any document that is not connected using a ``toctree`` will not
show up in the Table of Contents and is only available when linked to directly
using, for example, the ``:doc::`` role.
