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

    When using any of the XSL-based templates you also need the following (the default template uses Twig and as such
    does not need this):

    -  `XSL extension for PHP`_

Using PEAR
----------

PEAR provides the latest released version of phpDocumentor and is an easy
way to set up your machine.

You can prepare your PEAR installation using the following commands::

    $ pear channel-discover pear.phpdoc.org

And to install phpDocumentor you can use the following command::

    $ pear install phpdoc/phpDocumentor

When the installation is finished you can invoke the ``phpdoc``
command from any path in your system. Recommended is to read the
:doc:`../getting-started` section, which will explain how to start using
phpDocumentor.

PHAR
----

You can download the latest PHAR file from http://www.phpdoc.org/phpDocumentor.phar.

.. important::

   Some installations of PHP can have trouble executing the phar file. If you
   have any issues, please consult the following website first:
   http://silex.sensiolabs.org/doc/phar.html#pitfalls

Using Composer
--------------

Installing phpDocumentor using Composer_ is a matter of creating a directory to host your files and
executing the following command::

    $ composer require "phpdocumentor/phpdocumentor:2.*@beta"

This command can also be used to add phpDocumentor to your existing composer-based project

.. hint::

   phpDocumentor uses a fair number of other libraries, if you do not want those dependencies imported into your
   own project it is advised to use one of the other installation methods.

.. _Composer:               http:/getcomposer.org
.. _`PHP 5.3.3`:            http://www.php.net
.. _Graphviz:               http://graphviz.org/Download..php
.. _intl extension for PHP: http://www.php.net/intl
.. _XSL extension for PHP:  http://www.php.net/xsl

