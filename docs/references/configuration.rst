Configuration
=============

phpDocumentor is meant as a highly configurable and extensible application. As such there are a lot of things that can
be configured by the user.

An overview will be given in this chapter where you could place the configuration file and what it needs to look like.

Location
--------

The easiest solution would be to place the configuration file in the root of your project with the name
``phpdoc.dist.xml``. This file can be committed to a Revision Control System and thus will the settings always be
available.

When you have added a configuration file then you do not need to provide its location to phpDocumentor; the following
command suffices to build your documentation::

    $ phpdoc

An additional benefit is that it is possible for each developer to place a file called ``phpdoc.xml`` in their project,
in addition to the ``phpdoc.dist.xml``. This configuration file will be used instead of the ``phpdoc.dist.xml`` and when
added as an ignore rule in your VCS it will give developers the ability to have settings other than the project
defaults.

.. hint::

    When present, the file 'phpdoc.xml' is used instead of 'phpdoc.dist.xml' and thus does not supplement it.

Another option is to use the ``-c`` or ``--configuration`` arguments to tell phpDocumentor the location of your
configuration file. This can be convenient for centralized configuration management or using different settings per
environment.

.. note::

    ``none`` is a reserved word and providing ``-c none`` as option will result in any configuration file being ignored.

Basic configuration
-------------------

phpDocumentor follows the *convention over configuration* style and as such it is only necessary to specify the options
which you want to change with regard to the defaults.

The easiest way to find out what the defaults are is to look in the configuration template, which is located in
``[PHPDOC FOLDER]/data/phpdoc.tpl.xml`` or to examine the specifications in this document.

Usually the following configuration suffices for your project::

    <?xml version="1.0" encoding="UTF-8" ?>
    <phpdoc>
      <parser>
        <target>data/output</target>
      </parser>
      <transformer>
        <target>data/output</target>
      </transformer>
      <files>
        <directory>.</directory>
      </files>
    </phpdoc>

The configuration expects you to specify for both what their output(target) folder should be.

.. note::

    By separating the output locations into one for the parser and one for the transformation process it is possible to
    provide a staging location where you indefinitely store your structure file and benefit from the increased speed
    when doing multiple runs. This is called **Incremental Processing** or **Incremental Parsing**.

The transformer expects the source file to be at the target location of the parser so you need not specify that
explicitly.

The files section allows you to specify where the source code for your project is and what files to ignore.

::

      <files>
        <file>test.php</file>
        <file>bin/*</file>
        <directory>src</directory>
        <directory>tes??</directory>
        <ignore>test/*</ignore>
      </files>

It is allowed to use relative paths here; just remember that these are relative from the the location of your
configuration file.

It is possible to specify specific files or a specific set of **files** using the ``file`` element. As with the
``-f`` parameter it supports wildcards.

In addition you can also provide entire **directory** trees using the element. This also supports the use of wildcards.
Please note that, in contrary to the ``file`` element, the ``directory`` element is recursive and will tell
phpDocumentor to process all files contained in this folder and every subfolder.

In some cases you will have to **ignore** certain files in your project; examples of these can be third party libraries
and/or tests. In this case you can use the ``ignore`` element and provide a pattern (not a path) to ignore. Thus if you
provide ``*test*`` it will ignore any file or directory containing the text *test* in it.

The *starting point* or *base directory* for the ignore directive is the *Project Root*; which is the highest folder
that all files share in common. Thus if you provide a single directory and that does not contain any parseable files
and only on subfolder (which does contain parseable files) then the *Project Root* if not the given folder but the
subfolder.

Reference
---------

The phpDocumentor configuration file contains the following top level
elements which are explained in more detail in the sub-chapters.

-  Title, the title for this project, *may contain HTML*
-  Parser, all settings related to the conversion of the source
   code to the intermediate structure file (structure.xml).
-  Transformer, all settings related to the process of transforming
   the intermediate structure file (structure.xml) to a human readable
   format, such as HTML or PDF.
-  Logging, all settings related to the generation of logs.
-  Transformations, the default template and additional
   transformations can be specified in this section.
-  Files, a fileset where to mention which files and folders to include and
   which to ignore.

Title
~~~~~

The title is a single element used to alter the logo / text section identifying
for which project the documentation is generated.

It is possible to use HTML in order, for example, include a logo in the text.

*Example*

::

    <title><![CDATA[<b>My</b> Project]]></title>

Parser
~~~~~~

The parser section contains all settings related to the conversion
of your project's source to the intermediate structure format of
phpDocumentor (structure.xml).

The following fields are supported:

-  *default-package-name*, optional element which defines the name of the
   default package. This is the name of the package when none is provided.
-  *target*, the target location where to store the structure.xml,
   also used as source location for the transformer. This can be either a
   relative or absolute folder.
   Relative folders are relative to the location of the configuration file.
-  *markers*, contains a listing of prefixes used in single line comments to
   mark a segment of code using a single word (by default FIXME and TODO
   are supported).

   Example::

       // TODO: do something

-  *extensions*, contains a list of extension's which a file
   must have to be interpreted. If a file does not have the extension
   mentioned in this list then it is not parsed.
   By default these are: php, php3 and phtml.

*Example*

::

    <parser>
      <target>output</target>
      <markers>
        <item>TODO</item>
        <item>FIXME</item>
      </markers>
      <extensions>
        <extension>php</extension>
        <extension>php3</extension>
        <extension>phtml</extension>
      </extensions>
    </parser>

Transformer
~~~~~~~~~~~

The transformer section contains most settings related to the
transformation of the intermediate structure format (structure.xml)
to a human-readable set of documentation. The format of this set of
documentation is determined by the template choice which is present
in the ``transformations`` head section.

.. NOTE::

    The transformer determines the location of the intermediate
    structure format (structure.xml) by retrieving the ``target``
    element in the ``parser`` section.


The following fields are supported:


- *target*, the target location where to store the generated
  documentation files.
- *external-class-documentation* (*v0.14.0*), with this element you can link the
  documentation generated by phpDocumentor to the URL of a library based on the
  prefix of the class. This element may be used multiple times and each time
  has a ``prefix`` and ``uri`` element which specify which class to link where.
  The ``uri`` element supports 2 substitution variables: {CLASS} and
  {LOWERCASE_CLASS}.

      Please note that if the class is part of a namespace that
      the backslashes are also copied; with exception of the 'root' (start of the
      class name).

*Example*

::

    <transformer>
        <target>output</target>
        <external-class-documentation>
            <prefix>HTML_QuickForm2</prefix>
            <uri>http://pear.php.net/package/HTML_QuickForm2/docs/latest/HTML_QuickForm2/{CLASS}.html</uri>
        </external-class-documentation>
    </transformer>

Logging
~~~~~~~

The logging section contains all settings related to the logging of
information in phpDocumentor.

.. NOTE::

    phpDocumentor does not 'care' whether the specified logging paths exist;
    if they do not then no log files are generated.

The following fields are supported:

-  *level*, determines the minimum level of information that is
   supplied. Any priority equal to or higher than the given is
   included in the log files and is output to the screen. All
   priorities lower than the given are not logged. The following
   values are allowed (in order from highest to lowest priority):

   - emerg
   - alert
   - crit
   - err
   - warn
   - notice
   - info
   - debug
   - quiet

-  *paths*, contains all folders to where phpDocumentor may log.
-  *default*, this is the path of the default logging file, the
   name may be augmented with a {DATE} variable to provide a
   timestamp and {APP_ROOT} to indicate the root of the phpDocumentor application.
-  *errors*, messages with level *debug* are not added to the
   default log but in a separate log file whose path you can declare
   here. As with the *default* log file you can augment the path with
   the {DATE} variable.

*Example*:

::

    <logging>
        <level>warn</level>
        <paths>
            <default>{APP_ROOT}/data/log/{DATE}.log</default>
            <errors>{APP_ROOT}/data/log/{DATE}.errors.log</errors>
        </paths>
    </logging>

Transformations
~~~~~~~~~~~~~~~

The transformations section controls the behaviour applied in
transforming the intermediate structure format to the final human-readable
output.

The following fields are supported:

- *template*, the name or path of a template to use. This element may be used
  multiple times to combine several templates though usually you only supply one.
  Example::

      <template name="responsive"/>

  ::

      <template name="/home/mvriel/phpDocumentor Templates/myTemplate"/>

- *transformation*, it is also possible to execute additional transformations
  specifically for this project by defining your own transformations here.

*Example*:

::

    <transformations>
        <template name="responsive" />
    </transformations>

Files
~~~~~

Please see the previous sub-chapter `Basic configuration`_ for a complete
description of the files section.

*Example*

::

      <files>
        <file>test.php</file>
        <file>bin/*</file>
        <directory>src</directory>
        <directory>tes??</directory>
        <ignore>test/*</ignore>
      </files>

Appendix A: basic configuration example
---------------------------------------

::

    <?xml version="1.0" encoding="UTF-8" ?>
    <phpdoc>
      <parser>
        <target>data/output</target>
      </parser>
      <transformer>
        <target>data/output</target>
      </transformer>
      <files>
        <directory>.</directory>
      </files>
    </phpdoc>

Appendix B: complete configuration example
------------------------------------------

::

    <?xml version="1.0" encoding="UTF-8" ?>
    <phpdoc>
        <title>My project</title>
        <parser>
            <target>output</target>
            <markers>
                <item>TODO</item>
                <item>FIXME</item>
            </markers>
            <extensions>
                <extension>php</extension>
                <extension>php3</extension>
                <extension>phtml</extension>
            </extensions>
            <visibility></visibility>
        </parser>
        <transformer>
            <target>output</target>
        </transformer>
        <logging>
            <level>warn</level>
            <paths>
                <default>{APP_ROOT}/data/log/{DATE}.log</default>
                <errors>{APP_ROOT}/data/log/{DATE}.errors.log</errors>
            </paths>
        </logging>
        <transformations>
            <template name="responsive" />
        </transformations>
        <files>
            <file>test.php</file>
            <file>bin/*</file>
            <directory>src</directory>
            <directory>tes??</directory>
            <ignore>test/*</ignore>
        </files>
    </phpdoc>
