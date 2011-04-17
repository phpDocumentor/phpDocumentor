README
======

What is DocBlox?
----------------
DocBlox is a documentation generator, just like phpDocumentor. Though the big difference is that it was written
with certain goals in mind:
- Performance
- Low memory usage
- PHP 5.3 compatible
- Integrates nicely with Continuous integration environments (incremental parsing)

The reason I am building this application is actually because the company which I work for currently
has a project which cannot be parsed by phpDocumentor (due to memory constraints) and I wanted to generate API docs.

I could write quite bit more about this project but will stick to this text for the moment.

Please keep in mind: this is pre-release software. It has been tested against Zend Framework, Symfony, Agavi, Solar
Framework and even larger projects but it does not yet contain the full feature set of phpDocumentor.

Requirements
------------
DocBlox requires the following:
- PHP 5.2.6 or higher (probably works on 5.2.x but I am unable to test this)
- Graphviz (optional, used for generating Class diagrams)
- The XSL extension, http://www.php.net/manual/en/book.xsl.php

How to use DocBlox?
-------------------

*NOTE*: DocBlox must be installed in a path without spaces in order to generate HTML files. The libraries that
DocBlox uses to transform XSL files do not reliably support spaces in the path.

### The easy way

The easiest way to run docblox is by running the following command:

    php {INSTALLATION_FOLDER}/bin/docblox.php

This will automatically execute the `project:run` task which kickstarts the parsing and transformation process.

### The more flexible but still easy way
Under the hood DocBlox takes a two step approach to generating documentation:
1. parse the source files and create a XML file (structure.xml) containing all metadata
2. transform the XML file to human readable output (currently only static HTML is supported)

Parsing
-------
The parsing is accomplished by executing the task project:parse using the docblox.php executable in the bin directory.
This file takes several arguments (see `{INSTALLATION_FOLDER}/bin/docblox.php project:parse -h` for a full list) and with it you can select which files or directories to parse and which to ignore.

Example:

    php {INSTALLATION FOLDER}/bin/docblox project:parse -d /home/me/project

The example above parses the /home/me/project directory for any file suffixed with .php and write a structure.xml file to the data/output directory.

By executing this command you construct the building block for the transformation process, the meta data store a.k.a. structure.xml.

Please note: if an existing structure.xml is found on the target location it will attempt to check every target file if it has changed. If not, it will not re-parse and thus it will reuse the existing definition. Speeding up the process.

Transformation
--------------
The transformation process is responsible for creating human-readable output from the generated XML file.

You can start the transformation process by running

   php {INSTALLATION FOLDER}/bin/transform.php

By default it will look in the output subfolder (_unless executed from the DocBlox root folder_) of your current working directory for the structure.xml file.
During the transformation the structure.xml file will be interpreted and (static) XHTML files will be created in the same folder, additionally a search index and class diagram will be generated.

That is all, now you have the API documentation of your project.
If you want to see which options are available for transformation, see `./bin/docblox.php project:transform -h` for a full list.

Documentation
-------------
For more detailed information you can check our online documentation at http://www.docblox-project.org/documentation

Known issues
------------

1. Search does not work / is not available when accessing the documentation locally from Google Chrome.
  Google Chrome blocks access to local files (and thus the search index) using Javascript when working with local files (file://); it is not possible for us to fix this.
2. Remembering which navigation items are open does not work when accessing the documentation locally from Google Chrome.
  The code responsible for remembering which items are open uses cookies to track the state. Unfortunately Google Chrome disables the use of cookies when working with local files (file://); it is not possible for us to fix this.

Contact
-------
To come in contact is actually dead simple and can be done in a variety of ways.

# Twitter: @DocBlox
# IRC:     Freenode, #docblox
# Github:  http://www.github.com/mvriel/docblox
# Website: http://www.docblox-project.org
# E-mail:  mike.vanriel@naenius.com
