Installing
==========

System Requirements
-------------------

phpDocumentor has several dependencies on other software packages. Please make sure that you have these
installed before installing phpDocumentor.

-  `PHP 7.2.5`_ or higher
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

    $ docker pull phpdoc/phpdoc:3
    $ docker run --rm -v $(pwd):/data phpdoc/phpdoc

When the installation is finished you can invoke the ``phpdoc`` command from any path in your system.

And next
--------

It is recommended to read the :doc:`your-first-set-of-documentation` section next as it will explain how to quickly start using phpDocumentor.

.. _Composer:               https://getcomposer.org
.. _`PHP 7.2.5`:            https://www.php.net
.. _Graphviz:               https://graphviz.org/download/
.. _PlantUML:               https://plantuml.com/download
.. _Twig:                   https://twig.symfony.com/
.. _Phive website:          https://phar.io/
