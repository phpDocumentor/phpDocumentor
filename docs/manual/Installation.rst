Installation
============

DocBlox can easily be installed either via PEAR or manually from
Github at
`http://github.com/mvriel/docblox <http://github.com/mvriel/docblox>`_.

Independent to the chosen installation type has DocBlox several
dependencies on other software packages. Some of these dependencies
are only necessary when generating specific parts of the
documentation, such as PDFs or Graphs. If a dependency is only
limited to a subset of features it is denoted with the dependency
entry below.


-  PHP 5.2.6 or higher (5.2.5 and lower might work but is not supported, 5.3
   is explicitly supported)
-  XSL extension for PHP, only applicable when generating HTML via
   XSL (recommended)
-  Graphviz, only applicable when generating Graphs (recommended)
-  wkhtmltopdf, only applicable when generating PDFs (not enabled
   by default)

PEAR
----

PEAR provides the latest released version of DocBlox and is an easy
way to set up your machine.

You can prepare your PEAR installation using the following commands:

::

    $ pear channel-discover pear.docblox-project.org

And to install DocBlox you can use the following command:

::

    $ pear install DocBlox/DocBlox-beta

When the installation is finished you can invoke the ``docblox``
command from any path in your system. Recommended is to read the
chapter :doc:`Basic usage`, which will explain how to start using
DocBlox.

Manual Installation
-------------------

At http://docblox-project.org you can find the latest available release as
download. It is also possible to download the development version
though this is not recommended for production environments.

The steps necessary for manual installation are:


1. download your preferred installation archive from
   http://docblox-project.org
2. unzip in your favourite location
3. Depending upon your OS:

   a. **For Linux or Mac OSX**: create a symlink from <DOCBLOX\_PATH>/bin/docblox.php
      to your bin folder (usually /usr/bin) named ``docblox``.
   b. **For Windows**: Add <DOCBLOX\_PATH>/bin to your PATH so that you can invoke
      ``docblox.bat`` from any location.

When the installation is finished you can invoke the ``docblox``
command from any path in your system. Recommended is to read the
chapter :doc:`Basic usage`, which will explain how to start using
DocBlox.
