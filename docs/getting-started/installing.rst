Installing
==========

System Requirements
-------------------

phpDocumentor has several dependencies on other software packages. Please make sure that you have these
installed before installing phpDocumentor.

-  `PHP 7.2.0`_
-  Graphviz_ (optional)
-  PlantUML_ (optional)

Phive
----

   $ phive install phpDocumentor

For more information about phive have a look at their website_
Now you have phpDocumentor installed and it can be executed directly.


PHAR
----

You can download the latest PHAR file from https://github.com/phpDocumentor/phpDocumentor/releases.

The phar file can be used by simply invoking php and providing the phar file as a parameter::

  $ php phpDocumentor.phar -d . -t docs/api


Docker
----

1. `$ docker pull phpdoc/phpdoc`
2. `$ docker run --rm -v $(pwd):/data phpdoc/phpdoc`

When the installation is finished you can invoke the ``phpdoc`` command from any path in your system. It is recommended
to read the :doc:`../getting-started/index` section next as it will explain how to quickly start using phpDocumentor.

Using Composer
--------------

.. important::

   This method of installation was initially provided as a convenience; with time however it was common for
   dependency conflicts between phpDocumentor and the host application to arise. The phpDocumentor team does
   not recommend this installation method and is unable to provide support on issues stemming from
   dependency conflicts.

Installing phpDocumentor using Composer_ is a matter of creating a directory to host your files and executing the
following command::

    $ composer require "phpdocumentor/phpdocumentor:^3.0"

This command can also be used to add phpDocumentor to your existing composer-based project

.. hint::

   phpDocumentor uses a fair number of other libraries, if you do not want those dependencies imported into your
   own project it is advised to use one of the other installation methods.

.. _Composer:               https://getcomposer.org
.. _`PHP 7.2.0`:            https://www.php.net
.. _Graphviz:               https://graphviz.org/download/
.. _PlantUML:               https://plantuml.com/download
.. _Twig:                   https://twig.sensiolabs.org
.. _website:                https://phar.io/
