Changing the Look and Feel
==========================

Overview
--------

With this tutorial, I want to show you how the look and feel of phpDocumentor can be changed using one of the
existing :term:`templates` or by selecting a custom-made :term:`template`.

What is a template?
-------------------

To be fair, the title of this tutorial is mildly misleading. Why? A template in phpDocumentor means so much
more than just the look and feel of your generated documentation.

A template in phpDocumentor is a series of actions, called :term:`transformations`, that is capable of crafting the
desired output. With this mechanism, it is possible to generate HTML, XML, PDF but also to copy files to a destination
location or generate a report of errors found while scanning your project.

This is possible because a :term:`template` is a collection of those :term:`transformations`; that can combine
the assets of a template and your project's structure information into a set of documentation.

Selecting a template
--------------------

phpDocumentor has several different templates_ out-of-the-box to customize the look and feel of your documentation.
The ``default`` template has been created to be fully customizable with just CSS. We will explain later on this page how
you can customize this ``default`` template.

.. note::

The number of templates provided by phpDocumentor have been reduced a lot. Maintaining a template takes a lot of time.
Therefore we dropped all XML based templates that were provided by phpDocumentor 2. For people who developed an XML template
in the past a new ``xml`` template has been added with the same structure as in v2. This should allow you to transform the
XML manually to recreate your documentation.

To apply a template other than the default you can add the ``--template`` option::

    $ phpdoc -d "./src" -t "./docs/api" --template="clean"

Using multiple templates at once
--------------------------------

Sometimes you want to generate multiple formats at the same time; you could run phpDocumentor multiple times but it is
more efficient to pass multiple templates. phpDocumentor will then optimize the generation of documentation and not
re-run those steps that they have in common.

You can instruct phpDocumentor to use multiple templates by using a comma-separated list::

    $ phpdoc -d "./src" -t "./docs/api" --template="xml,clean"

Here you can see how both the ``xml`` template and the ``clean`` template are applied; which results in both
HTML documentation and an XML structure document.

Using a custom template
-----------------------

When you have a company or project-branded template you can also use that with phpDocumentor by providing the location
of your template's folder::

    $ phpdoc -d "./src" -t "./docs/api" --template="data/templates/my_template"

In the above example is demonstrated how a custom template in a folder ``data/templates/my_template``, relative to
the current working directory is being used to generate documentation.

Adding templates to configuration
---------------------------------

In the chapters above we demonstrated how you can use a specific template using the command-line, but it is also
possible to use a :doc:`configuration<../references/configuration>` file to describe which template should be used for
your project.

This can be done by adding an element called ``template`` in the root of your XML document.

Here is an example where the clean and XML templates are used:

.. code-block:: xml

    <?xml version="1.0" encoding="utf-8" ?>

    <phpdocumentor>
        ...
        <template name="clean">
        <template name="xml">
    </phpdocumentor>

Customizing the look and feel
-----------------------------

phpDocumentor allows you to customize the look and feel with some small steps. When you specify a build-in template
phpDocumentor will first look in `<project_root>/.phpdoc/template` for files. By adding your twig file in this
directory you can customize parts of the generated HTML. For example, when you want to remove breadcrumbs,
you create an empty file named `breadcrumbs.html.twig` in `.phpdoc/template/`.
Have a look in the `template directory`_ which other files can be overwritten.

Creating your own look and feel
-------------------------------

It is also possible to create your custom template using Twig as a templating engine. This can be done by
extending, or re-using, parts of an existing template or by starting from scratch. phpDocumentor offers a lot of
conveniences for template writers, which would go beyond the scope of this tutorial.

A tutorial for creating your custom documentation with Twig is offered in the chapter
:doc:`creating-your-own-template-using-twig`, for a complete overview of all options and possibilities see the guide
on :doc:`creating templates<../guides/templates>` how to accomplish this.

If you want to tweak one or two things it is also possible to define :term:`transformations` directly in your
configuration file. This way you can override the index, copy files (such as PDFs) or generate additional documents.

For example, here we see how a PDF (located at ``data/specification.pdf`` of the template folder) is copied to the
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

.. _templates: http://www.phpdoc.org/templates
.. _template directory: https://github.com/phpDocumentor/phpDocumentor/tree/master/data/templates/default
