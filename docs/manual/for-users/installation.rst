Installation
============

phpDocumentor can easily be installed either via PEAR or manually using the
installer at `http://github.com/phpdocumentor/phpdocumentor2 <http://github.com/phpdocumentor/phpdocumentor2>`_.

Requirements
------------

phpDocumentor has several dependencies on other software packages. Some of
these dependencies are only necessary when generating specific parts of the
documentation, such as Graphs. If a dependency is only
limited to a subset of features it is denoted with the dependency
entry below.

-  `PHP 5.3.3 <http://www.php.net>`_
-  `XSL extension for PHP <http://www.php.net/xsl>`_, only applicable when
   generating HTML via XSL (required)
-  `Graphviz <http://graphviz.org>`_, only applicable when generating Graphs (recommended)

.. warning::
   phpDocumentor can not install these dependencies and will generate errors if
   they are missing.

Manual Installation
-------------------

phpDocumentor provides a stand alone installer that will perform most of the
actions necessary for a smooth installation.

The steps for a manual installation are:

1. Download the installer from
   https://raw.github.com/phpDocumentor/phpDocumentor2/develop/installer.php
   to the intended location. We will refer to the intended location as <PHPDOC\_PATH>.
2. Run the installer::

       php installer.php

3. Set up your binaries to use phpDocumentor from any location:

   a. **For Linux or Mac OSX**: create a symlink from <PHPDOC\_PATH>/bin/phpdoc.php
      to your bin folder (usually /usr/bin) named ``phpdoc``.
   b. **For Windows**: Add <PHPDOC\_PATH>/bin to your PATH so that you can invoke
      ``phpdoc.bat`` from any location.

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

Running phpDocumentor
---------------------

When the installation is finished you can invoke the ``phpdoc``
command from any path in your system. Recommended is to read the
:doc:`basic-usage` chapter, which will explain how to start using
phpDocumentor.
