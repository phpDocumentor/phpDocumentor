Installing
==========

System Requirements
-------------------

phpDocumentor has several dependencies on other software packages. Please make sure that you have these
installed before installing phpDocumentor.

-  `PHP 5.3.3`_
-  `intl extension for PHP`_
-  Graphviz_

.. note::

    Some of the templates make use of the XSL templating language; these templates need the following dependency as
    well. By default phpDocumentor uses a template based on the Twig_ templating language; which does not have
    this requirement.

    -  `XSL extension for PHP`_

Using PEAR
----------

PEAR provides the latest released version of phpDocumentor and is an easy way to set up your machine.

You can prepare your PEAR installation using the following commands::

    $ pear channel-discover pear.phpdoc.org

And to install phpDocumentor you can use the following command::

    $ pear install phpdoc/phpDocumentor

When the installation is finished you can invoke the ``phpdoc`` command from any path in your system. It is recommended
to read the :doc:`../getting-started/index` section next as it will explain how to quickly start using phpDocumentor.

PHAR
----

You can download the latest PHAR file from http://www.phpdoc.org/phpDocumentor.phar.

.. important::

   Some installations of PHP can have trouble executing the phar file. If you have any issues, please consult the
   following website first: http://silex.sensiolabs.org/doc/phar.html#pitfalls

The phar file can be used by simply invoking php and providing the phar file as a parameter::

  $ php phpDocumentor.phar -d . -t docs/api

Using Composer
--------------

Installing phpDocumentor using Composer_ is a matter of creating a directory to host your files and executing the
following command::

    $ composer require "phpdocumentor/phpdocumentor:2.*"

This command can also be used to add phpDocumentor to your existing composer-based project

.. hint::

   phpDocumentor uses a fair number of other libraries, if you do not want those dependencies imported into your
   own project it is advised to use one of the other installation methods.

.. _Composer:               http:/getcomposer.org
.. _`PHP 5.3.3`:            http://www.php.net
.. _Graphviz:               http://graphviz.org/Download..php
.. _intl extension for PHP: http://www.php.net/intl
.. _XSL extension for PHP:  http://www.php.net/xsl
.. _Twig:                   http://twig.sensiolabs.org
