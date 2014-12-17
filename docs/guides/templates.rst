Writing your own Template
=========================

Overview
--------

phpDocumentor has an advanced templating system that, due to its flexibility, requires a bit of background. Themes in
most other applications require you to follow a strict series of required files and folders in order to work. Templating
in phpDocumentor allows you to be completely free in choosing how you organize your template.

This enables you to generate not just HTML documentation but also XML output. PDFs, markdown files and more. What is
perhaps even better is that users of templates can combine multiple templates during a single documentation run to
produce multiple types of output simultaneously.

.. sidebar::

   **More Than a Theme**

   In phpDocumentor we explicitly talk about having templates and not themes. This is because a template is the
   groundwork for numerous types of output, the template definition merely lays out what needs to happen to make it so.
   A theme however represents a consistent output (such as HTML documentation) whose styling can be altered by placing
   a layer on top of it.

How Does it Work?
-----------------

What happens is that phpDocumentor reads a special configuration file, called ``template.xml``, that contains some
background information on the template (such as author) and a series of action definitions that will determine what the
template exactly does.

These action definitions are called *transformations*.

Each transformation is responsible for a single specific task execution and provides parameters and options to
delegate the execution of that task to a *Writer*.

For example:

    In order to have a fully functional website showing the API Documentation there is a CSS file included with the
    template. This CSS file needs to be copied to the destination location. As such there is a specific transformation
    in the template definition that describes: the Writer responsible for File Operations should move that file
    to a folder named 'css' in the target location.

In addition to the example above there are writers that are capable of generating a Checkstyle report of errors, one or
more HTML files using a single twig template and more. Please see :doc:`../references/writers/index` for a complete
list of writers and their actions.

Almost each writer needs to know the following:

* A source location where to get its input from.
* A destination location where to write a file to (if applicable).
* and possible a query with which it can limit the operations performed on the source.

phpDocumentor will go through each of the transformations in the order where they are defined in the ``template.xml``
and may set state that can be remembered between each of the transformations.

Location & Configuration
------------------------

Templates can be located in the ``data/templates`` folder of phpDocumentor, or a custom location of your chosing. All
templates have in common that they are governed by a special configuration file called ``template.xml``.

template.xml
phpdoc.xml

Templates
---------

routing

Writers
-------

More Than a Theme
-----------------

:doc:`templates/structure`
:doc:`templates/twig`
:doc:`templates/xsl`

Related topics
--------------

* :doc:`../references/writers/index`, for a complete list of available writers
