Building your own branding using templates
==========================================

Most of the time it is only necessary to replace the graphical
layout with that of your own project. This is an easy process and
requires little explanation on how templating actually works.
Please consult the chapter *Quick version* when the above is
what you desire.

Do you want to control every detail of how your template looks then
it is recommended to read on; later in this document will more theory be
provided on how branding works in DocBlox.

Quickstart
----------

I will try to give a step-by-step description how to create your own branding:

1. Create a new folder in your intended location (for example your project) with
   the name of the intended template. Please make sure *no* spaces are present in
   the path as the XSL Writer (or actually libxsl) cannot handle that.
2. invoke the following command::

       $ docblox template:generate -t <target path> -n <template name>

   ..

       Optionally you can also provide **--author** to provide an author name and
       **--version** to provide a version number to your template.

   The command above will generate a skeleton template for you in the intended
   location.
3. Alter the template.css in the css sub-folder to apply your own styling and/or
   alter the index.xsl to accommodate for your own layout.

   **Important:** Unless you are absolutely sure what you are doing; please keep
   the tabular structure intact because a 100% height scenario is desirable and
   due to the need for iframes it is necessary to use a tabular structure
   instead of the box model.

   ..

       If you know a solution to change the tabular structure into box model;
       please contact us via the methods mentioned in the README file.

Theory of operation
-------------------

In many applications branding usually means that you create a complete template
from scratch. This gives you full freedom but you need to update your template for
every new release of the application that you are building your template for.

With DocBlox this is different. Instead of building a template from scratch you
can 'extend' an existing template and alter those bits that are undesirable.
This provides the power needed to make a template that requires less maintenance
and benefits from any new features introduced in later versions.

Key concepts
------------

In order to extend an existing template it is necessary to understand certain
key words of phrases.

* **Transformation**, an action that transforms the collected data (or parts
  there-of) into the desired output format using Writers.
  Transformations can also have parameters with which the transformation
  process can be influenced.

  Examples are:

  * XSL, transforms parts of the collected data into a HTML file
  * Graph, transforms parts of the collected data into diagrams
  * PDF, transforms parts of the collected data into a PDF file
  * and more.

* **Template definition**, this is a collection of transformations, parameters
  and meta-data contained in the template.xml file in your template root.

* **Root template**, this is the XSL template that is mentioned in the
  template.xml; this is your starting point for extending XSL templates.

Template definition
-------------------

The template definition is the starting point for every template. This is
a XML file defining which transformations need to be executed using which
writers.

Example::

    <?xml version="1.0" encoding="utf-8"?>

    <template>
      <description>This is my new template</description>
      <author>Mike van Riel</author>
      <email>mike.vanriel@naenius.com</email>
      <version>1.0.0</version>
      <transformations>
        <transformation query="copy" writer="FileIo" source="js" artifact="js"/>
        <transformation query="copy" writer="FileIo" source="images" artifact="images"/>
        <transformation query="copy" writer="FileIo" source="templates/abstract/css" artifact="css"/>
        <transformation query="copy" writer="FileIo" source="templates/my_template/css" artifact="css"/>
        <transformation query="copy" writer="FileIo" source="templates/my_template/js" artifact="js"/>
        <transformation query="" writer="xsl" source="templates/my_template/index.xsl" artifact="index.html"/>
        <transformation query="" writer="xsl" source="templates/abstract/sidebar.xsl" artifact="nav.html">
            <parameters>
                <variables>
                    <section.dashboard.show>false</section.dashboard.show>
                    <section.api.show/>
                    <section.namespaces.show/>
                    <section.packages.show/>
                    <section.files.show/>
                    <section.files.show-elements>false</section.files.show-elements>
                    <section.reports.show>false</section.reports.show>
                    <section.charts.show>false</section.charts.show>
                </variables>
            </parameters>
        </transformation>
        <transformation query="/project/file/@path" writer="xsl" source="templates/abstract/api-doc.xsl" artifact="{$path}"/>
        <transformation query="" writer="sourcecode" source="" artifact=""/>
        <transformation query="" writer="xsl" source="templates/abstract/graph_class.xsl" artifact="graph.html"/>
        <transformation query="" writer="Graph" source="Class" artifact="classes.svg" />
      </transformations>
    </template>

Basically the example above demonstrates that, in addition to some meta-data,
this template executes 11 transformations. Below is a simple explanation of
what is done. Each writer is discussed more in-depth in later chapters.

* The first 5 use the FileIo writer to copy assets to subfolders of the target
  location
* The 3 subsequent transformations use the XSL writer to transform a template
  into a HTML file (where the second uses parameters to augment the process).
* Then the transformation uses the sourcecode writer to process embedded
  source files.
* The last two transformation use the XSL writer to create a container page and
  the Graph writer generates an image of a Class Diagram.

Meta-data
~~~~~~~~~

A template definition can contain any amount of meta-data but DocBlox uses the
following for its internal workings:

* description [REQUIRED], provides a description of the contents or looks of
  this template. It is required in order to package the template.
* author [REQUIRED], the name of the author of this package; multiple elements
  are allowed and used.
* version [REQUIRED], the version of this template.
* dependencies, structure indicating whether this template depends on another
  template or plugin.
  May contain the following sub-elements:

  * template, this template depends on the given template and cannot function
    without.
  * plugin, this template depends on the given plugin and cannot function
    without.

..

    Please note that the name is explicitly **not** mentioned here; the name of
    the template is derived from the name of the folder in which the template
    resides.

Example::

    <description>
    <template>
      <description>This is my new template</description>
      <author>Mike van Riel</author>
      <email>mike.vanriel@naenius.com</email>
      <version>1.0.0</version>
      <dependencies>
        <template>abstract</template>
        <plugin>core</plugin>
      </dependencies>
      ...
    </template>

Transformations
~~~~~~~~~~~~~~~

Transformations are composed of 5 elements:

* **writer**, the name of the Writer that is going to execute the transformation.
  See the `Appendix: Writers`_ chapter for a list of available writer and what
  they do.
* **query**, A writer-specific specialisation; via this element can the
  transformation focus the writer's functionality. Please read the writer's
  chapter in the appendix for more details.
* **source**, the location or path to the source data which feeds the
  transformation. Some writers (such as Sourcecode) do not use the source
  attribute.

      If a path is indicated by this attribute then the root for relative paths
      is always [docblox]/data.

* **artifact**, the target path where to write the artifact to. This is usually
  a filename but could also be a folder. Some writers (such as Sourcecode) do
  not use the artifact attribute.

      If a path if indicated by this attribute then the root for relative paths
      if always the provided transform's target location.

* **parameters**, some writers support additional parameters. These can be passed
  using this element. The parameters element supports nested data.

Example::

        <template>
          ...
          <transformations>
            <transformation query="copy" writer="FileIo" source="js" artifact="js"/>
            <transformation query="" writer="xsl" source="templates/my_template/index.xsl" artifact="index.html"/>
            <transformation query="" writer="xsl" source="templates/abstract/sidebar.xsl" artifact="nav.html">
                <parameters>
                    <variables>
                        <section.charts.show>false</section.charts.show>
                    </variables>
                </parameters>
            </transformation>
            <transformation query="/project/file/@path" writer="xsl" source="templates/abstract/api-doc.xsl" artifact="{$path}"/>
            <transformation query="" writer="sourcecode" source="" artifact=""/>
            <transformation query="" writer="Graph" source="Class" artifact="classes.svg" />
          </transformations>
        </template>

Tips
~~~~

1. The order in your definition matters for execution. This can be used as an
   advantage if you would like to 'override' a whole file by overwriting it in
   a later transformation.
   This is often done to copy CSS folders from a donor template and then
   overwrite the template.css with a custom variant.

2. Start with copying the js folder from /data. This folder contains a jQuery
   library that is ready to use.
   Similarly, consider copying the image folder from /data. This folder contains
   some clipart used throughout the DocBlox templates.

3. If you want your template to support the --sourcecode argument of DocBlox
   then you need to include the Sourcecode writer.

Building a template
-------------------

Introduction
~~~~~~~~~~~~

DocBlox uses the **abstract** template as basis for its own templates. This
template can easily be customized and contains a multitude of hooks and
xsl:templates to adapt. This without affecting or duplicating large parts of the
rest of the template.

As such it is advised to use the basis of this template and use CSS to restyle
it to your liking.

Should something prove hard to do, please submit a pull request to the
http://github.com/docblox/template.abstract repository with a generic solution
or mail your request.

    Please note that the Abstract template intentionally does not use the
    Box model for the layout of the index page. This is needed since iframes
    cannot fluidly fill the remaining height without tables.

    Iframes are a necessity for larger projects to decrease bandwidth and
    increase performance as navigation sidebars can become quite large.

Steps
~~~~~

Depending on the needed level of customization you have the following steps to
go through when creating a template:

1. Generate a template skeleton using the ``template:generate`` method
2. Alter the template.css file in the css folder
3. Extend the index.xsl or api-doc.xsl file with new or overridden xsl:templates
4. Edit the generated template.xml and insert your own values and writers.

That is the gist of it. In the following chapters we will discuss this more
in-depth.

    Generating a template is covered in the `Quickstart`_ and will not be covered
    in the proceeding chapters.

CSS
~~~


Tips
++++

1. You can change the menu and header by overriding the

XSL:Templates
~~~~~~~~~~~~~

Root templates and overriding
+++++++++++++++++++++++++++++

To ease overriding templates all root templates (those directly invoked by a
transformation) contain the xsl:includes for every 'child-template' file.
When creating your own templates; keep this in mind. Anyone wanting to extend
your template will be thankful for it.

Every root template will result in a HTML file upon transformation and is
advised to have at least the following:

1. an include of the chrome.xsl file of the Abstract template
2. a template named 'content'.

Example::

    <xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
      <xsl:output indent="yes" method="html" />
      <xsl:include href="chrome.xsl" />

      <xsl:template name="content">
        My content
      </xsl:template>

    </xsl:stylesheet>

The chrome.xsl file is responsible for the basic layout and HTML chrome. It will
invoke the xsl:template named *content* in the body.

Extending
+++++++++

What is what in the Abstract template
+++++++++++++++++++++++++++++++++++++


Appendix: Writers
-----------------

Checkstyle
~~~~~~~~~~

FileIo
~~~~~~

Graph
~~~~~

PDF
~~~

Search
~~~~~~

Sourcecode
~~~~~~~~~~

XSL
~~~