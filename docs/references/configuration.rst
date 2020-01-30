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

Another option is to use the ``-c`` or ``--config`` arguments to tell phpDocumentor the location of your
configuration file. This can be convenient for centralized configuration management or using different settings per
environment.

.. note::

    ``none`` is a reserved word and providing ``-c none`` as option will result in any configuration file being ignored.

Basic configuration
-------------------

phpDocumentor follows the *convention over configuration* style and as such it is only necessary to specify the options
which you want to change with regard to the defaults.

The best way to write your configuration is by linking our xsd file in your configuration file. This way xml editors
can help you to discover the elements that can be added in your configuration.

Usually the following configuration suffices for your project::

    <?xml version="1.0" encoding="UTF-8" ?>
    <phpdocumentor
            configVersion="3"
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns="http://www.phpdoc.org"
            xsi:noNamespaceSchemaLocation="http://docs.phpdoc.org/latest/phpdoc.xsd"
    >
        <paths>
            <output>build/api</output>
            <cache>build/cache</cache>
        </paths>
        <version number="3.0.0">
            <api>
                <source dsn=".">
                    <path>src</path>
                </source>
            </api>
        </version>
    </phpdocumentor>

The configuration expects you to specify for both what their output(target) folder should be.

.. note::

    By separating the output locations into one for the parser and one for the transformation process it is possible to
    provide a staging location where you indefinitely store your structure file and benefit from the increased speed
    when doing multiple runs. This is called **Incremental Processing** or **Incremental Parsing**.

phpDocumentor automatically uses the cache directory when possible there is way to configure this.

The ``api/source`` section allows you to specify where the source code for your project is and what files to ignore. The
``dsn`` attribute specifies the location of your project. Currently only relative and absolute paths are supported.
A relative ``dsn`` is relative to the location of your config file.

::

    <source dsn="file:///my/project">
        <path>test.php</path>
        <path>bin/*</path>
        <path>src</path>
        <path>tes??</path>
        <path>test/**/*</path>
    </source>

The paths in source are relative to the ``dsn``; It is not possible to use absolute paths in a path.

The **path** element does support glob patterns in include files and directories. When no wildcards are included
the path is expected to be a directory tree that needs to be included. Thus ``src`` will include all files under ``src``.

Reference
---------

The phpDocumentor configuration file contains the following top-level
elements which are explained in more detail in the sub-chapters.

- paths
- version
- setting
- template

Paths
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Paths is forming the base output location of phpDocumentor. More specific output locations can be specified in the ``version`` element.

``output`` is the base path to place the output of the ``transformation`` stage.
``cache`` it the base path to store the cache used by phpDocumentor during the ``parsing`` stage.

::

    <paths>
        <output>string</output>
        <!--Optional:-->
        <cache>string</cache>
    </paths>

Version
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Version is the main element to instruct phpDocumentor what needs to be done. A project could have multiple versions.

Each version defined in a config MUST have a unique ``number`` attribute. And may have one or more ``api`` or ``guide`` elements.

To have more control where the output of each version is stored a version may have a ``folder`` element. The folder element
is a compliment to the ``paths/output`` defined path. When ``folder`` is omitted the output of a version is stored in
``paths/output`` without any additional paths.

::

    <version number="latest">
        <folder>latest</folder>
        <api> <!-- optional --> </api>
        <guide> <!-- optional --> </guide>
    </version>

.. note::
  Currently only single version projects are supported. The configuration format is prepared to support multiple.

Api
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The api element part of a ``version`` it describes a project source api that needs to be processed by phpDocumentor.
A minimal setup of ``api`` only contains ``source`` element.

::

   <api>
      <source dsn="./path/to/project">
        <path>src</path>
      </source>
    </api>

Also ``api` may contain an ``output`` element that forms the full path to the location where the rendered docblock api
is located. The value of ``output`` is appended to the ``paths/output`` element and the optional ``folder`` element
of its version.

In some cases you will have to **ignore** certain files in your project; examples of these can be third party libraries
and/or tests. In this case you can use the ``ignore`` element and provide a pattern (not a path) to ignore. Thus if you
provide ``*test*`` it will ignore any file or directory containing the text *test* in it.

See Appendix B for a full example of the options available in ``api``



Appendix A: basic configuration example
---------------------------------------

::

    <?xml version="1.0" encoding="UTF-8" ?>
    <phpdocumentor
            configVersion="3"
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns="http://www.phpdoc.org"
            xsi:noNamespaceSchemaLocation="http://docs.phpdoc.org/latest/phpdoc.xsd"
    >
        <paths>
            <output>build/api</output>
            <cache>build/cache</cache>
        </paths>
        <version number="3.0.0">
            <api>
                <source dsn=".">
                    <path>src</path>
                </source>
            </api>
        </version>
    </phpdocumentor>

Appendix B: complete configuration example
------------------------------------------

::

    <?xml version="1.0" encoding="UTF-8" ?>
    <phpdocumentor configVersion="3.0">
      <paths>
        <output>build/docs</output>
        <!--Optional:-->
        <cache>string</cache>
      </paths>
      <!--Zero or more repetitions:-->
      <version number="3.0">
        <!--Optional:-->
        <folder>latest</folder>
        <!--Zero or more repetitions:-->
        <api format="php">
          <source dsn=".">
            <!--1 or more repetitions:-->
            <path>src</path>
          </source>
          <!--Optional:-->
          <output>api</output>
          <!--Optional:-->
          <ignore hidden="true" symlinks="true">
            <!--1 or more repetitions:-->
            <path>tests</path>
          </ignore>
          <!--Optional:-->
          <extensions>
            <!--1 or more repetitions:-->
            <extension>php</extension>
          </extensions>
          <!--Optional:-->
          <visibility>private</visibility>
          <!--Optional:-->
          <default-package-name>MyPackage</default-package-name>
          <!--Optional:-->
          <include-source>true</include-source>
          <!--Optional:-->
          <markers>
            <!--1 or more repetitions:-->
            <marker>TODO</marker>
            <marker>FIXME</marker>
          </markers>
        </api>
        <!--Zero or more repetitions:-->
        <guide format="rst">
          <source dsn=".">
            <!--1 or more repetitions:-->
            <path>support/docs</path>
          </source>
          <!--Optional:-->
          <output>docs</output>
        </guide>
      </version>
      <!--Zero or more repetitions:-->
      <setting name="string" value="string"/>
      <!--Zero or more repetitions:-->
      <template name="string" location="string">
        <!--Zero or more repetitions:-->
        <parameter name="string" value="string"/>
      </template>
    </phpdocumentor>
