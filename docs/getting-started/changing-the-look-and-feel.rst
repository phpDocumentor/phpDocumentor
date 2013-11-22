Changing the Look and Feel
==========================

Overview
--------

With this tutorial I want to show you how the look and feel of phpDocumentor can be changed using one of the
existing :term:`templates` or by selecting a custom-made :term:`template`.

What is a template?
-------------------

To be fair, the title of this tutorial is mildly misleading. Why? Because a template in phpDocumentor means so much
more than just the look and feel of your generated documentation.

.. sidebar:: It used to be themes

   When phpDocumentor2 was created templates were still called 'themes' because originally their focus was on displaying
   a different skin for the output. During alpha we had to rename them to templates as it matched the functionality
   much more.

A template in phpDocumentor is a series of actions, called :term:`transformations`, that is capable of crafting a
desired output. With this mechanism it is possible to generate HTML, XML, PDF but also to copy files to a destination
location or generate a report of errors found while scanning you project.

This is possible because a :term:`template` is a collection of those :term:`transformations`; that can combine
the assets of a template and your project's structure information into a set of documentation.

Selecting a template
--------------------

phpDocumentor has several different templates_ out-of-the-box to customize the look and feel of your documentation, and
more are released on a regular basis. With these templates it is also possible to generate your documentation, or parts
thereof, in different formats. An example of this is the ``checkstyle`` template, with which you can generate an XML
file containing all documentation errors discovered by phpDocumentor in the checkstyle XML format.

To apply a template other than the default you can add the ``--template`` option::

    $ phpdoc -d "./src" -t "./docs/api" --template="checkstyle"

With the above command phpDocumentor will no longer output HTML output but just the XML output containing all errors
and warnings.

Using multiple templates at once
--------------------------------

Sometimes you want to generate multiple formats at the same time; you could run phpDocumentor multiple times but it is
more efficient to pass multiple templates. phpDocumentor will then optimize the generation of documentation and not
re-run those steps that they have in common.

You can instruct phpDocumentor to use multiple templates by using a comma-separated list::

    $ phpdoc -d "./src" -t "./docs/api" --template="checkstyle,clean"

Here you can see how both the ``checkstyle`` template and the ``clean`` template are applied; which results in both
HTML documentation and an XML Checkstyle error report.

Using a custom template
-----------------------

When you have a company or project-branded template you can also use that with phpDocumentor by providing the location
of your template's folder::

    $ phpdoc -d "./src" -t "./docs/api" --template="data/templates/my_template"

In the above example is demonstrated how a custom template in a folder ``data/templates/my_template``, relative to the
current working directory, is being used to generate documentation with.

Adding templates to configuration
---------------------------------

In the chapters above we demonstrated how you can use a specific template using the command-line, but it is also
possible to use a :doc:`configuration<../references/configuration>` file to describe which template should be used for
your project.

This can be done by adding an element called ``template`` to the element ``transformation`` in the root of your XML
document.

Here is an example where the clean and checkstyle templates are used:

.. code-block:: xml

    <?xml version="1.0" encoding="utf-8" ?>

    <phpdocumentor>
        ...
        <transformations>
            <template name="clean"/>
            <template name="checkstyle"/>
        </transformations>
    </phpdocumentor>

Creating your own look and feel
-------------------------------

It is also possible to create your own template using either XSL or Twig as templating engine. This can be done by
extending, or re-using, parts of an existing template or by starting from scratch. phpDocumentor offers a lot of
conveniences for template writers, which would go beyond the scope of this tutorial.

See the guide on :doc:`creating templates<../guides/templates>` how to accomplish this.

If you want to tweak one or two things it is also possible to define :term:`transformations` directly in your
configuration file. This way you can override the index, copy files (such as PDFs) or generate additional documents.

For example, here we see how a PDF (located at ``data/specification.pdf`` of the project) is copied to the
destination location (the target folder) so that it may be referred to, and linked to, in the documentation.

.. code-block:: xml

    <?xml version="1.0" encoding="utf-8" ?>

    <phpdocumentor>
        ...
        <transformations>
            <template name="clean"/>
            <transformation writer="FileIO" query="copy" source="data/specification.pdf" artifact="specification.pdf" />
        </transformations>
    </phpdocumentor>

Read more
---------

* :doc:`../guides/templates`
* :doc:`../references/writers/index`

.. _templates: http://www.phpdoc.org/templates
