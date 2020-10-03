Installing
==========

System Requirements
-------------------

phpDocumentor has several dependencies on other software packages. Please make sure that you have these
installed before installing phpDocumentor.

-  `PHP 7.4.0`_ or higher
-  Graphviz_ (optional)
-  PlantUML_ (optional)

Phive
-----

   $ phive install phpDocumentor

Once you run the command, phpDocumentor will be installed and it can be executed directly.

For more information about Phive have a look at the `Phive website`_.


PHAR
----

You can download the latest PHAR file from https://github.com/phpDocumentor/phpDocumentor/releases.

The phar file can be used by invoking PHP directly and providing the phar file as a parameter::

   $ php phpDocumentor.phar -d . -t docs/api


Docker
------

    $ docker pull phpdoc/phpdoc
    $ docker run --rm -v $(pwd):/data phpdoc/phpdoc

When the installation is finished you can invoke the ``phpdoc`` command from any path in your system.

Using Composer
--------------

.. important::

   This method of installation was initially provided as a convenience; with time, however, it was common for
   dependency conflicts between phpDocumentor and the host application to arise. The phpDocumentor team does
   not recommend this installation method and is unable to provide support on issues stemming from
   dependency conflicts.

Installing phpDocumentor using Composer_ is a matter of creating a directory to host your files and executing the
following command::

   $ composer require "phpdocumentor/phpdocumentor:^3.0"

This command can also be used to add phpDocumentor to your existing composer-based project.

After installation, you can invoke phpDocumentor from the root of your project using the ``vendor/bin/phpdoc`` command.

.. hint::

   phpDocumentor uses a fair number of other libraries, if you do not want those dependencies imported into your
   own project it is advised to use one of the other installation methods.


It is recommended to read the :doc:`your-first-set-of-documentation` section next as it will explain how to quickly start using phpDocumentor.

.. _Composer:               https://getcomposer.org
.. _`PHP 7.4.0`:            https://www.php.net
.. _Graphviz:               https://graphviz.org/download/
.. _PlantUML:               https://plantuml.com/download
.. _Twig:                   https://twig.symfony.com/
.. _Phive website:          https://phar.io/
