Configuration
=============

The configuration component is a much simpler version of the implementation of phpDocumentor 2. In phpDocumentor 2 we
used JMS Serializer to convert an Configuration file into a series of classes but this has the downside that the
Configuration is quite rigid and JMS Serializer introduces a fair number of Composer dependencies.

With phpDocumentor 3 we want to be able to load an XML (no other format; no foreseeable need for other formats and
only adds complexity) and return an array of strings containing a normalized form of the configuration settings.

It is important to note that the ConfigurationFactory receives a URI and should be able to load a configuration file
over HTTP so that people can directly refer to a config file on Github and that phpDocumentor checks out everything
using the settings in the Configuration file.

Another important note is that the Configuration format should change for phpDocumentor 3 but we want the
ConfigurationFactory to load phpDocumentor 2 based configuration files as well. So the Factory needs to detect which
type of Configuration file it is loading, load the file and output a uniform array with settings.

This is what I expect that the ConfigurationFactory class will be going to look like:

.. image:: diagrams/configuration.png

Because the configuration location may be overridden during the execution of the application we have introduced a
`replaceLocation` method that will allow the Console Command to set a new location for the Configuration.

An example of the new Configuration file should look similar to this::

    <?xml version="1.0" encoding="utf-8"?>

    <phpdocumentor
        version="3"
        xmlns="http://www.phpdoc.org" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.phpdoc.org phpdoc.xsd"
    >
        <paths>
            <output>file://build/docs</output>
            <cache>/tmp/phpdoc-doc-cache</cache>
        </paths>
        <version number="1.0.0">
            <output>latest</output>
            <api format="php">
                <source dsn=”file://.”>
                    <path>src</path>
                </source>
                <ignore hidden=”true” symlinks=”true”>
                    <path>src/ServiceDefinitions.php</path
                </ignore>
         <extensions>
             <extension>php</extension>
         </extensions>
         <visibility>public</visibility>
         <default-package-name>Default</default-package-name>
         <markers>
             <marker>TODO</marker>
             <marker>FIXME</marker>
         </markers>
            </api>
            <guide format="rst">
                <source dsn=”file://../phpDocumentor/phpDocumentor2”>
                    <path>docs</path>
                </source>
            </guide>
        </version>
        <template name="clean"/>
        <template location="https://github.com/phpDocumentor/phpDocumentor2/tree/develop/data/templates/clean"/>
    </phpdocumentor>

where the version of phpDocumentor 2 looks like this::

    <?xml version="1.0" encoding="UTF-8" ?>
    <phpdocumentor>
        <parser>
            <default-package-name>Default</default-package-name>
            <encoding>utf-8</encoding>
            <target>output/build</target>
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
            <files>
                <ignore-hidden>true</ignore-hidden>
                <ignore-symlinks>true</ignore-symlinks>
            </files>
        </parser>
        <transformer>
            <target>output</target>
        </transformer>
        <logging>
            <level>error</level>
        </logging>
        <transformations>
            <template name="clean"/>
        </transformations>
        <translator>
            <locale>en</locale>
        </translator>
    </phpdocumentor>

The issue to implement / refactor the above can be found here:
https://github.com/phpDocumentor/phpDocumentor2/issues/1522
