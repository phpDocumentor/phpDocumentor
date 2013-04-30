Basic Layout
============

A template in phpDocumentor2 is in its most basic form a folder
containing a template.xml file.

Standalone additionally require a composer.json file.


..

    To build a template specific to your project, you don't need to put it into
    a separate repository but can distribute it with your project. Simply specify
    the path to that template as name of the template to use.


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
        <transformation writer="xsl" source="templates/my_template/index.xsl" artifact="index.html"/>
        <transformation writer="xsl" source="templates/abstract/sidebar.xsl" artifact="nav.html"/>
        <transformation query="/project/file/@path" writer="xsl" source="templates/abstract/api-doc.xsl" artifact="{$path}"/>
        <transformation writer="sourcecode" source="" artifact=""/>
        <transformation writer="xsl" source="templates/abstract/graph_class.xsl" artifact="graph.html"/>
        <transformation writer="Graph" source="Class" artifact="classes.svg" />
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

A template definition can contain any amount of meta-data but phpDocumentor uses the
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
      is always [phpdoc]/data.

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
   some clipart used throughout the phpDocumentor templates.

3. If you want your template to support the --sourcecode argument of phpDocumentor
   then you need to include the Sourcecode writer.



Composer definition
-------------------

To build a standalone phpDocumentor2 template, you need to make it a
`Composer <http://getcomposer.org>`_ packages, like every component of the
documentor.

The project name in the composer file must start with ``template-``, the template name
used in the install will be what follows after. For example the template "new-black"
has the name ``template-new-black``. A template also needs to specify the attribute
``type: phpdocumentor-template``. Currently, all templates must be in the
namespace ``phpdocumentor``.

Templates must depend on the phpdocumentor/unified-asset-installer which is
used to install them in the right location. If they extend a base template,
this should be specified as well.

As an example, see the composer.json of the new-black template::

    {
        "name": "phpdocumentor/template-new-black",
        "type": "phpdocumentor-template",
        "description": "Web 2.0 template with dark sidebar for phpDocumentor",
        "keywords": ["documentation", "template", "phpdoc"],
        "homepage": "http://www.phpdoc.org",
        "license": "MIT",
        "require": {
            "ext-xsl": "*",
            "phpdocumentor/unified-asset-installer": "1.*",
            "phpdocumentor/template-abstract": "1.*"
        }
    }

