[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Travis Status](https://img.shields.io/travis/phpDocumentor/phpDocumentor2.svg?label=Linux)](https://travis-ci.org/phpDocumentor/phpDocumentor2)
[![Appveyor Status](https://img.shields.io/appveyor/ci/phpDocumentor/phpDocumentor2.svg?label=Windows)](https://ci.appveyor.com/project/phpDocumentor/phpDocumentor2/branch/develop)
[![Coveralls Coverage](https://img.shields.io/coveralls/github/phpDocumentor/phpDocumentor2.svg)](https://coveralls.io/github/phpDocumentor/phpDocumentor2?branch=develop)
[![Scrutinizer Code Coverage](https://img.shields.io/scrutinizer/coverage/g/phpDocumentor/phpDocumentor2.svg)](https://scrutinizer-ci.com/g/phpDocumentor/phpDocumentor2/?branch=develop)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/phpDocumentor/phpDocumentor2.svg)](https://scrutinizer-ci.com/g/phpDocumentor/phpDocumentor2/?branch=develop)
[![Stable Version](https://img.shields.io/packagist/v/phpDocumentor/phpDocumentor.svg)](https://packagist.org/packages/phpDocumentor/phpDocumentor)
[![Unstable Version](https://img.shields.io/packagist/vpre/phpDocumentor/phpDocumentor.svg)](https://packagist.org/packages/phpDocumentor/phpDocumentor)
[![Downloads](https://img.shields.io/packagist/dm/phpDocumentor/phpDocumentor.svg)](https://packagist.org/packages/phpDocumentor/phpDocumentor)


phpDocumentor
======

What is phpDocumentor?
----------------

phpDocumentor is an application that is capable of analyzing your PHP source code and
DocBlock comments to generate a complete set of API Documentation.

Inspired by phpDocumentor 1 and JavaDoc it continues to innovate and is up to date
with the latest technologies and PHP language features.

Features
--------

phpDocumentor supports the following:

* *PHP 7.0 compatible*, full support for Namespaces, Closures and more is provided.
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

*Please note* that phpDocumentor 3 is still under heavy development. We aim to add all features needed to have full support
for php 7+. But at this moment that is not the case.

Requirements
------------

phpDocumentor requires the following:

* PHP 7.1 or higher
* ext/iconv, http://php.net/manual/en/book.iconv.php (is enabled by default since PHP 5.0.0)
* ext/intl, http://php.net/manual/en/book.intl.php
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

1. Using the PHAR
2. Via [Docker](https://hub.docker.com/r/phpdoc/phpdoc/)
3. Via [Composer](https://getcomposer.org)

_*Please note* that it is required that the installation path of phpDocumentor does not
contain spaces. This is a requirement imposed by an external library (libxml)_

### Using the PHAR

1. Download the phar file from https://github.com/phpDocumentor/phpDocumentor2/releases
2. ???
3. Profit!

### Via docker

1. `$ docker pull phpdoc/phpdoc`
2. `$ docker run --rm -v $(pwd):/data phpdoc/phpdoc`

### Via Composer

1. phpDocumentor is available on [Packagist](https://packagist.org/packages/phpDocumentor/phpDocumentor).
2. It can be installed as a dependency of your project by running

        $ composer require --dev phpdocumentor/phpdocumentor dev-master

Afterwards you are able to run phpDocumentor directly from your `vendor` directory:

    $ php vendor/bin/phpdoc

*Please note* that we are not able to be compatible with all types of setups. In
some situations phpDocumentor will block updates of other packages. We do not recommend
using composer to install phpDocumentor.

### Pear
Starting from phpDocumentor v3 we decided to drop pear support. We will provide the
already released versions of phpDocumentor v2. But these versions won't be maintained.

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

For more detailed information you can check our online documentation at [http://phpdoc.org/docs/latest/index.html](http://phpdoc.org/docs/latest/index.html).

Contact
-------

To come in contact is actually dead simple and can be done in a variety of ways.

* Twitter: [@phpDocumentor](http://twitter.com/phpDocumentor)
* Website: [http://www.phpdoc.org](http://www.phpdoc.org)
* Github:  [http://www.github.com/phpDocumentor/phpDocumentor2](http://www.github.com/phpDocumentor/phpDocumentor2)
* E-mail:  [mike@phpdoc.org](mailto:mike@phpdoc.org)
