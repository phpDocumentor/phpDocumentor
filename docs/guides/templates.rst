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

Location & Configuration
------------------------

template.xml
phpdoc.xml

Folder layout
-------------

routing

Transformations
---------------

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
