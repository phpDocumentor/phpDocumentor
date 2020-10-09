[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](https://opensource.org/licenses/MIT)
![Qa workflow](https://github.com/phpDocumentor/phpDocumentor/workflows/Qa%20workflow/badge.svg)
[![Code Coverage](https://scrutinizer-ci.com/g/phpDocumentor/phpDocumentor/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/phpDocumentor/phpDocumentor/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/phpDocumentor/phpDocumentor/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/phpDocumentor/phpDocumentor/?branch=master)
![Packagist Version](https://img.shields.io/packagist/v/phpdocumentor/phpdocumentor?label=packagist%20stable)
![Packagist Pre Release Version](https://img.shields.io/packagist/vpre/phpdocumentor/phpdocumentor?label=packagist%20unstable)
[![Downloads](https://img.shields.io/packagist/dm/phpDocumentor/phpDocumentor.svg)](https://packagist.org/packages/phpDocumentor/phpDocumentor)


phpDocumentor
=============

What is phpDocumentor?
----------------------

phpDocumentor is an application that is capable of analyzing your PHP source code and
DocBlock comments to generate a complete set of API Documentation.

Inspired by phpDocumentor 1 and JavaDoc it continues to innovate and is up to date with the latest technologies and PHP language features.

phpDocumentor v2 (stable)
-------------------------

The current stable version of phpDocumentor is v2.9.1, which can be used to generate documentation for PHP
applications up to PHP version 7.0.
phpDocumentor is an old tool that doesn't support all 7.0 introduced features.
It will not process any scalar type hints and requires docblock tags to generate API documentation.
If your application/library is using PHP 7.0 or lower consult our [v2 branch] for installation instructions and detailed information about how to use phpDocumentor.

phpDocumentor v3 (under development)
------------------------------------

**Note:**
The phpDocumentor team is currently working on a new phpDocumentor generation.
It will have full support for the latest PHP features, so you can take advantage of all native type hints.
v3 is still under development and is not stable enough to use in a daily development flow.

### Nightly builds

PhpDocumentor doesn't have a nightly release.
However, during each pipeline a [phar] artifact is built.
If you want to test the bleeding edge version of phpDocumentor, have a look in the [actions] section of this repository.
Each successful QA workflow has a download at the right upper corner.

Installation
------------

PhpDocumentor requires PHP 7.2 or higher to run.
However, code of earlier PHP versions can be analyzed.

All templates provided with phpDocumentor have support for Class diagrams based on the read code base.
This will require the application [Graphviz] to be installed on the machine running phpDocumentor.
Rendering the class diagrams using [Graphviz] is optional, and warnings about missing [Graphviz] can be ignored.
However, your documentation will contain some dead links in this case. Class diagram will be created with option `--setting=graphs.enabled=true`.

There are 4 ways to install phpDocumentor:

1. Using phive (recommended)
2. Using the PHAR (manual install)
3. Via [Docker]
4. Via [Composer]

### Using Phive

`$ phive install --force-accept-unsigned phpDocumentor`

For more information about phive have a look at their [website](https://phar.io/).
Now you have phpDocumentor installed, it can be executed like this:

`php tools/phpDocumentor`

### Using the PHAR

1. Download the phar file from https://github.com/phpDocumentor/phpDocumentor/releases
2. You can execute the phar like this: `php phpDocumentor.phar`

### Via Docker

1. `$ docker pull phpdoc/phpdoc`
2. `$ docker run --rm -v $(pwd):/data phpdoc/phpdoc`

### Via Composer (not recommended)

1. phpDocumentor is available on [Packagist](https://packagist.org/packages/phpDocumentor/phpDocumentor).
2. It can be installed as a dependency of your project by running

        $ composer require --dev phpdocumentor/phpdocumentor dev-master

Afterwards you are able to run phpDocumentor directly from your `vendor` directory:

    $ php vendor/bin/phpdoc

*Please note* that we cannot support all configurations.
In some situations, phpDocumentor will block updates of other packages.
We do not recommend using Composer to install phpDocumentor.

### PEAR

Starting from phpDocumentor v3 we decided to drop PEAR support due to declining use.
We will provide the already released versions of phpDocumentor v2, but these versions will be community maintained so that the team can focus on phpDocumentor 3.

How to use phpDocumentor?
-------------------------

The easiest way to run phpDocumentor is by running the following command:

    $ phpdoc run -d <SOURCE_DIRECTORY> -t <TARGET_DIRECTORY>

This command will parse the source code provided using the `-d` argument and output it to the folder indicated by the `-t` argument.

phpDocumentor supports a whole range of options to configure the output of your documentation.
You can execute the following command, or check our website, for a more detailed listing of available command-line options.

    $ phpdoc run -h

Configuration file(s)
---------------------

phpDocumentor also supports the use of configuration files (named phpdoc.xml or phpdoc.dist.xml by default).
Please consult the documentation to see the format and supported options.

Documentation
-------------

For more detailed information you can check our online documentation at https://docs.phpdoc.org/.

Features
--------

phpDocumentor supports the following:

* *PHP 7.0 compatible*, full support for Namespaces, Closures and more are provided.
* *Shows any tag*, some tags add additional functionality to phpDocumentor (such as @link).
* *Processing speed*, Zend Framework experiences a significant reduction in processing time compared to phpDocumentor 1.
* *Low memory usage*, peak memory usage for small projects is less than 20MB, medium projects 40MB and large frameworks 100MB.
* *Incremental parsing*, if you kept the Structure file from a previous run, you get an additional performance boost of up
  to 80% on top of the mentioned processing speed increase above.
* *Easy template building*, if you want to make a branding you only have to call 1 task and edit 3 files.
* *Command-line compatibility with phpDocumentor 1*, phpDocumentor 2 is an application in its own right but the basic phpDocumentor 1 arguments, such as --directory, --file and --target, have been adopted.
* *Two-step process*, phpDocumentor first generates a cache with your application structure before creating the output.
  If you'd like you can use that to power your own tools or formatters!

phpDocumentor 3 is still under development.
We will add all features that are needed to have full support for PHP 7.0 and higher.

Contact
-------

Reaching out to us is easy, and can be done with:

* Twitter: [@phpDocumentor]
* Website: https://www.phpdoc.org
* GitHub:  https://www.github.com/phpDocumentor/phpDocumentor
* E-mail:  [mike@phpdoc.org]

[@phpDocumentor]: https://twitter.com/phpDocumentor
[v2 branch]: https://github.com/phpDocumentor/phpDocumentor/tree/2.9
[Graphviz]: https://www.graphviz.org/download/
[actions]: https://github.com/phpDocumentor/phpDocumentor/actions?query=workflow%3A%22Qa+workflow%22+is%3Asuccess
[Docker]: https://hub.docker.com/r/phpdoc/phpdoc/
[Composer]: https://getcomposer.org/
[mike@phpdoc.org]: mailto:mike@phpdoc.org
