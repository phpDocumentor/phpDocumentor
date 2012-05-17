Installation
============

phpDocumentor can easily be installed either via PEAR or manually from
Github at
`http://github.com/phpdocumentor/phpdocumentor2 <http://github.com/phpdocumentor/phpdocumentor2>`_.

Independent to the chosen installation type has phpDocumentor several
dependencies on other software packages. Some of these dependencies
are only necessary when generating specific parts of the
documentation, such as PDFs or Graphs. If a dependency is only
limited to a subset of features it is denoted with the dependency
entry below.

-  PHP 5.3.3
-  XSL extension for PHP, only applicable when generating HTML via
   XSL (recommended)
-  Graphviz, only applicable when generating Graphs (recommended)

phpDocumentor does not install these dependencies and will generate errors if they
are missing.

PEAR
----

PEAR provides the latest released version of phpDocumentor and is an easy
way to set up your machine.

You can prepare your PEAR installation using the following commands::

    $ pear channel-discover pear.phpdoc.org

And to install phpDocumentor you can use the following command::

    $ pear install phpdoc/phpDocumentor-alpha

When the installation is finished you can invoke the ``phpdoc``
command from any path in your system. Recommended is to read the
:doc:`basic-usage` chapter, which will explain how to start using
phpDocumentor.

Manual Installation
-------------------

At https://github.com/phpDocumentor/phpDocumentor2/tags you can find the latest
available release as download. It is also possible to download the `development
version <https://github.com/phpDocumentor/phpDocumentor2/downloads>`_ though
this is not recommended for production environments.

The steps necessary for manual installation are:

1. Download your preferred installation archive from
   https://github.com/phpDocumentor/phpDocumentor2/tags.
2. Unzip in your favourite location
3. Follow the Installation and Usage instructions at
   http://getcomposer.org/doc/00-intro.md#installation
4. Set up your binaries to use phpDocumentor from any location:

   a. **For Linux or Mac OSX**: create a symlink from <PHPDOC\_PATH>/bin/phpdoc.php
      to your bin folder (usually /usr/bin) named ``phpdoc``.
   b. **For Windows**: Add <PHPDOC\_PATH>/bin to your PATH so that you can invoke
      ``phpdoc.bat`` from any location.

When the installation is finished you can invoke the ``phpdoc``
command from any path in your system. Recommended is to read the
:doc:`basic-usage` chapter, which will explain how to start using
phpDocumentor.