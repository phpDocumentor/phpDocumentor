phpDocumentor Twig Plugin
=========================

.. important:: this document is still a work in progress

.. important::

   This plugin is still experimental. No templates have been created yet with it
   thus it is possible it needs some tweaks for optimal usability.

Installation
------------

This plugin is installed by default with phpDocumentor.

Features
--------

This plugin features the following:

* A writer to generate output using Twig
* A basic extension with the most common functions and tasks related to phpDocumentor.
* An interface for Twig extensions for third-party developers to base their
  Twig extensions on.

Generating output using Twig
----------------------------

The Twig writer can be used in any transformation in a template (see
phpDocumentor's main documentation on *Creating templates*).

The following line can be added as child to the transformations element::

    <transformation
        writer="\phpDocumentor\Plugin\Twig\Transformer\Writer\Twig"
        source="templates/my_twig_template/index.twig"
        artifact="index.html"/>

This tells phpDocumentor to use the Twig writer to use the ``index.twig``
template in the ``/data/templates/my_twig_template`` folder to generate the
``index.html`` file in your destination folder.

Full basic example template::

    <?xml version="1.0" encoding="utf-8"?>
    <template>
      <author>Mike van Riel</author>
      <email>mike.vanriel@naenius.com</email>
      <version>1.0.0</version>
      <copyright>Mike van Riel 2012</copyright>
      <description></description>
      <transformations>
          <transformation
            query="copy"
            writer="FileIo"
            source="templates/my_twig_template/stylesheet.css"
            artifact="stylesheet.css"/>
        <transformation
            writer="\phpDocumentor\Plugin\Twig\Transformer\Writer\Twig"
            source="templates/my_twig_template/index.twig"
            artifact="index.html"/>
      </transformations>
    </template>

Looping through a resultset
~~~~~~~~~~~~~~~~~~~~~~~~~~~

Using transformation it is possible to loop through a set of elements and apply
a template on all of them.

Easiest is to demonstrate this using an example:

*Suppose that you would want to generate a detailed view of a class using the
same template file*

You do not know up front which classes there will be so you would need a dynamic
transformation. This is possible by adding the 'query' attribute to your
transformation and specifying a xpath lookup.

Example transformation for each class entry::

    <transformation
        query="/project/file/class"
        writer="\phpDocumentor\Plugin\Twig\Transformer\Writer\Twig"
        source="templates/my_twig_template/class.twig"
        artifact="class.html"/>

.. important::

   Never use a ``//`` in your search query; this is impossibly slow and
   is to be avoided in all circumstances.

The above example would loop through all ``class`` elements of the project
and pass that to your twig template as root element instead of ``project``.

**There is a big problem with the transformation above:** the destination file
``class.html`` would be overwritten with each subsequent class found and you
end up with one file with the last class found.

To fix this you can use an xpath query in your artifact attribute as well to
create a dynamic destination filename.

Example transformation for each class entry with a dynamic file name::

    <transformation
        query="/project/file/class"
        writer="\phpDocumentor\Plugin\Twig\Transformer\Writer\Twig"
        source="templates/my_twig_template/class.twig"
        artifact="{@full_name}.html"/>

In the above example you can see that a xpath query is provided in braces. This
query is executed with the node that was found using the *query* attribute.
Thus in this example the ``full_name`` property was taken from the class that is
currently being processed.

Writing your own templates
--------------------------

.. note:: To be expanded upon

Every template receives a global Twig variable called ``ast_node``. This global
Twig variable represents the either the document-root of the Abstract Syntax
Tree (which is project) or a childnode if a Query has been used.

.. hint::

   if you were to do a query on ``/project/file/class`` then ``ast_node`` would
   be a single instance of /project/file/class.

Since the Abstract Syntax Tree (and its nodes) are presented as SimpleXMLElement
objects you can query them as normal objects from Twig.

Extensions
----------

Using third-party extensions
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

phpDocumentor allows you to add your own extensions so that they can be used.

Every extension needs to be available for autoloading (so it is common to
create a Plugin in this case and include them using the Composer 'require'
section).

Once available you can define a parameter 'twig-extension' in your template
header of with each individual transformation.

.. note::

   Extensions defined with a transformation override the ones defined in your
   template.

Example globally defined extension::

  <?xml version="1.0" encoding="utf-8"?>
  <template>
      <parameters>
          <twig-extension>
              \phpDocumentor\Plugin\MyPlugin\Twig\Extension
          </twig-extension>
      </parameters>
      <transformations>
          <transformation
              writer="\phpDocumentor\Plugin\Twig\Transformer\Writer\Twig"
              source="templates/sami/index.twig"
              artifact="index.html"/>
      </transformations>
  </template>

Example extension defined with an individual transformation::

  <?xml version="1.0" encoding="utf-8"?>
  <template>
      <transformations>
          <transformation
              writer="\phpDocumentor\Plugin\Twig\Transformer\Writer\Twig"
              source="templates/sami/index.twig"
              artifact="index.html"
          >
              <parameters>
                  <twig-extension>
                      \phpDocumentor\Plugin\MyPlugin\Twig\Extension
                  </twig-extension>
              </parameters>
          </transformation>
      </transformations>
  </template>

Writing your own extensions
~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. note:: To be written
