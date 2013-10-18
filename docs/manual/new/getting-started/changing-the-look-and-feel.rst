Changing the Look and Feel
==========================

.. note:: this is still a placeholder document; more content will be added

Applying a template
-------------------

phpDocumentor features several different templates_ to customize the look and feel of your documentation, or even to
generate alternate outputs. An example of this is the ``checkstyle`` template, with which you can generate an XML file
containing all documentation errors discovered by phpDocumentor.

To apply a template other than the default you can add the ``--template`` option::

    $ phpdoc -d ./src -t ./docs/api --template=checkstyle

With the above command phpDocumentor will no longer output HTML output but just the XML output containing all errors
and warnings. It is also possible to generate both at the same time by using a comma-separated list::

    $ phpdoc -d ./src -t ./docs/api --template=checkstyle,clean

Here you can see how both the ``checkstyle`` template and the ``clean`` template are applied.


Authoring Your Own Template
---------------------------

Building on an Existing Template
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Starting from Scratch
~~~~~~~~~~~~~~~~~~~~~

