README
======

What is phpDocumentor?
----------------

phpDocumentor an application that is capable of analyzing your PHP source code and
DocBlock comments to generate a complete set of API Documentation.

Inspired by phpDocumentor 1 and JavaDoc it continues to innovate and is up to date
with the latest technologies and PHP language features.

Features
--------

phpDocumentor supports the following:

* *PHP 5.3 compatible*, full support for Namespaces, Closures and more is provided.
* *Shows any tag*, some tags add additional functionality to phpDocumentor (such as @link).
* *Processing speed*, Zend Framework experienced a significant reduction in processing time compared to phpDocumentor 1.
* *Low memory usage*, peak memory usage for small projects is less than 20MB, medium projects 40MB and large frameworks 100MB.
* *Incremental parsing*, if you kept the Structure file from a previous run you get an additional performance boost of up
  to 80% on top of the mentioned processing speed above.
* *Easy template building*, if you want to make a branding you only have to call 1 task and edit 3 files.
* *Command-line compatibility with phpDocumentor 1*, phpDocumentor 2 is an application in its own right but the
  basic phpDocumentor 1 arguments, such as --directory, --file and --target, have been adopted.
* *Two-step process*, phpDocumentor first generates a cache with your application structure before creating the output.
  If you'd like you can use that to power your own tools or formatters!

Requirements
------------

phpDocumentor requires the following:

* PHP 5.3.3 or higher
* ext/iconv, http://php.net/manual/en/book.iconv.php (is enabled by default since PHP 5.0.0)
* ext/intl, http://php.net/manual/en/book.intl.php
* The XSL extension, http://www.php.net/manual/en/book.xsl.php (optional, only used with XSL based templates)
* Graphviz (optional, used for generating Class diagrams)

**Note:**
If you do not want to install the Graphviz dependency you are encouraged to generate your own template and make sure
that it does not contain anything related to `Graph`.
An easier solution might be to edit `data/templates/responsive/template.xml` file and remove every line
containing the word `Graph` but this will be undone with every upgrade of phpDocumentor.

Please see the documentation about creating your own templates for more information.

Installation
------------

There are 3 ways to install phpDocumentor:

1. Via PEAR (recommended)
2. Via [Composer](https://getcomposer.org)
3. Using the PHAR

_*Please note* that it is required that the installation path of phpDocumentor does not
contain spaces. This is a requirement imposed by an external library (libxml)_

### PEAR (recommended)

1. phpDocumentor is hosted on its own PEAR channel which can be discovered using the following command:

        $ pear channel-discover pear.phpdoc.org

2. After that it is a simple matter of invoking PEAR to install the application

        $ pear install phpdoc/phpDocumentor

### Via Composer

1. phpDocumentor is available on [Packagist](https://packagist.org).
2. It can be installed as a dependency of your project by running

        $ composer require-dev phpdocumentor/phpdocumentor

### Using the PHAR

1. Download the phar file from http://phpdoc.org/phpDocumentor.phar
2. ???
3. Profit!

How to use phpDocumentor?
-------------------

The easiest way to run phpDocumentor is by running the following command:

    $ phpdoc run -d <SOURCE_DIRECTORY> -t <TARGET_DIRECTORY>

This command will parse the source code provided using the `-d` argument and
output it to the folder indicated by the `-t` argument.

phpDocumentor supports a whole range of options to configure the output of your documentation.
You can execute the following command, or check our website, for a more detailed listing of available command line options.

    $ phpdoc run -h

Configuration file(s)
---------------------

phpDocumentor also supports the use of configuration files (named phpdoc.xml or phpdoc.dist.xml by default).
Please consult the documentation to see the format and supported options.

Documentation
-------------

For more detailed information you can check our online documentation at [http://phpdoc.org/docs/](http://phpdoc.org/docs/).

Known issues
------------

1. phpDocumentor must be installed in a path without spaces due to restrictions in libxml. The XSL transformation
   will throw all kinds of odd warnings if the path contains spaces.

Donations
---------

If you would like to help out financially we accept donations using [gittip](https://www.gittip.com/mvriel/). All
donations will be used to cover the costs for hosting phpDocumentor's website and PEAR repository.

Contact
-------

To come in contact is actually dead simple and can be done in a variety of ways.

* Twitter: [@phpDocumentor](http://twitter.com/phpdocumentor)
* Website: [http://www.phpdoc.org](http://www.phpdoc.org)
* IRC:     Freenode, #phpdocumentor
* Github:  [http://www.github.com/phpdocumentor/phpdocumentor2](http://www.github.com/phpdocumentor/phpdocumentor2)
* E-mail:  [mike.vanriel@naenius.com](mailto:mike.vanriel@naenius.com)
