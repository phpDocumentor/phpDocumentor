Validating documentation in your code
=====================================

Aside from generating API Documentation is DocBlox also capable of detecting
issues in your inline documentation

The default behaviour is that DocBlox will check for the following issues:

* Files

  * Missing DocBlocks
  * Missing a short description

* Classes / Interfaces

  * Missing DocBlocks
  * Missing a short description
  * More than one @package tag
  * More than one @subpackage tag
  * Having a @subpackage tag but no @package tag

* Methods / Functions

  * Missing DocBlocks
  * Missing a short description
  * Missing @param tags
  * Mismatching variable names in @param tags

* Properties

  * Missing DocBlocks
  * Missing a short description

Finding the issues
------------------

Finding what issues are encountered is not hard. It is perhaps overwhelmingly
simple as there are several ways to obtain this information. In the upcoming
sections we will touch every available method.

During parsing
~~~~~~~~~~~~~~

When you invoke DocBlox to analyze the sourcecode it will output any error found
to the screen.

Example::

    2011-12-22T17:37:28+01:00 ERR (3): No DocBlock was found for Function xhprof_percent_format
    2011-12-22T17:37:28+01:00 ERR (3): No short description for function xhprof_render_link
    2011-12-22T17:37:28+01:00 ERR (3): No DocBlock was found for Function print_pc_array
    2011-12-22T17:37:28+01:00 ERR (3): No DocBlock was found for Function print_symbol_summary
    2011-12-22T17:37:28+01:00 ERR (3): No DocBlock was found for File src/XHProf/utils/xhprof_runs.php
    2011-12-22T17:37:28+01:00 ERR (3): No DocBlock was found for Property $dir

.. WARNING::

    DocBlox will only report issues on files that it actually parses. If a file
    has already been processed before, and thus DocBlox does an incremental pass
    over your source code, no errors will be reported for that file.

.. NOTE::

    You can force DocBlox to do a complete re-parse of the source code (and thus
    mention any found issue) by using the ``--force`` argument.

In the Intermediate Structure File
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

DocBlox will trace these issues during parsing and store them in the
:term:`Intermediate Structure File` as *Parser markers* with the file where
they occur.

With this information you can build your own logger or even send them to a
centralized logger if needed.

Example::

    <file path="src/XHProf/display/xhprof.php" hash="756d6d2c38893417bc8c3e829654084f" package="DocBlox">
        <parse_markers>
            <error line="1">No DocBlock was found for File src/XHProf/display/xhprof.php</error>
            <notice line="58">Argument $ui_dir_url_path is missing from the function Docblock</notice>
            <error line="99">No DocBlock was found for Function xhprof_count_format</error>
            ...
        </parse_markers>
        ...
    </file>

In the generated documentation
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Another location where you can find these issues is by going to the
*Parsing errors* report in your generated documentation and review them there.

The basic information shown here is the file, line number and error that was
thrown but depending on the theme this could contain more, or less, information.

.. image:: /.static/for-users/validating-documentation-in-your-code/docs.png

As checkstyle log file
~~~~~~~~~~~~~~~~~~~~~~

DocBlox is also capable of outputting a log file in the
`checkstyle <http://checkstyle.sourceforge.net/>`_ format. The big advantage is
that Continuous Integration tools such as Jenkins or phpUnderControl can interpret
this format and keep statistics.

    The checkstyle format is the same one as used by phpCodeSniffer.

In order to generate this report you need to

1. install the checkstyle template::

       $ docblox template:install checkstyle

2. Run DocBlox and specify *checkstyle* as template::

       $ docblox -d [SOURCE] -t [TARGET] --template checkstyle

.. NOTE::

    You can also put this in a DocBlox configuration file. Using this
    configuration file it is even possible to generate your documentation
    *and* the checkstyle document at the same time by adding both templates.
    See the chapter on :doc:`configuration` for more details on this feature.

This will produce a file containing content similar to::

    <checkstyle version="1.3.0">
        <file name="Some/File.php">
            <error line="1" severity="error" message="Some kind of error" source="DocBlox.DocBlox.DocBlox"/>
            <error line="2" severity="critical" message="Some kind of critical issue" source="DocBlox.DocBlox.DocBlox"/>
            <error line="3" severity="notice" message="Some kind of notice" source="DocBlox.DocBlox.DocBlox"/>
            <error line="4" severity="warning" message="Some kind of warning" source="DocBlox.DocBlox.DocBlox"/>
        </file>
    </checkstyle>

DocBlox will specify the source as *DocBlox.DocBlox.DocBlox* which will then
translate to the *Category* and *Type* when reporting into build servers such
as Jenkins.

For more details on this feature and how to integrate it into Jenkins, see the
following blog post by Ben Selby: http://www.soulbroken.co.uk/blog/2011/10/produce-a-checkstyle-report-for-doc-block-validation-with-docblox/

Deprecating tags
----------------

With DocBlox it is possible to mark specific tags as being **deprecated** and
issue *Parser errors* when such a tag is encountered.

An example here would be a PHP5 project where the Coding Standards prescribe
that the *@access* tag may not be used. This can be caught by DocBlox.

You can specify which tags to deprecate by adding these as options to the 'Core'
plugin.

Example:

.. code-block:: xml
   :linenos:

    <docblox>
        ...
        <plugins>
            <plugin path="Core">
                <option name="deprecated">
                    <tag name="access" />
                    <tag name="return">
                        <element>DocBlox_Reflection_File</element>
                        <element>DocBlox_Reflection_Class</element>
                        <element>DocBlox_Reflection_Interface</element>
                        <element>DocBlox_Reflection_Property</element>
                    </tag>
                </option>
            </plugin>
            ...
        </plugins>
    </docblox>

Line 7 through 12 show another example where DocBlox only shows an error with
specific elements. The names shown are the class names of the Reflection
component and can be one of the following elements:

* DocBlox_Reflection_File
* DocBlox_Reflection_Class
* DocBlox_Reflection_Interface
* DocBlox_Reflection_Constant
* DocBlox_Reflection_Property
* DocBlox_Reflection_Variable
* DocBlox_Reflection_Function
* DocBlox_Reflection_Method
* DocBlox_Reflection_Include

Requiring tags
--------------

With DocBlox it is possible to mark specific tags as being **required** and issue
*Parser errors* when such a tag is not encountered with a specific element.

An example here would be a PHP5 project where the Coding Standards prescribe
that the *@return* tag is required with a method or function. This can be caught
by DocBlox.

You can specify which tags to require by adding these as options to the 'Core'
plugin. DocBlox only shows an error with specific elements. The names shown are the
class names of the Reflection component and can be one of the following elements:

* DocBlox_Reflection_File
* DocBlox_Reflection_Class
* DocBlox_Reflection_Interface
* DocBlox_Reflection_Constant
* DocBlox_Reflection_Property
* DocBlox_Reflection_Variable
* DocBlox_Reflection_Function
* DocBlox_Reflection_Method
* DocBlox_Reflection_Include

Example:

.. code-block:: xml
   :linenos:

    <docblox>
        ...
        <plugins>
            <plugin path="Core">
                <option name="required">
                    <tag name="return">
                        <element>DocBlox_Reflection_Method</element>
                        <element>DocBlox_Reflection_Function</element>
                    </tag>
                </option>
            </plugin>
            ...
        </plugins>
    </docblox>

