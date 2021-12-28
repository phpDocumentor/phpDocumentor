Installation
============

System Requirements
-------------------

.. note::

    If you are using the official Docker container, or the Github Action, you do not need to worry about this
    and can directly skip to the chapter `Using Docker`_.

phpDocumentor has several dependencies on other software packages. Please make sure that you have these
available before installing phpDocumentor.

-  `PHP 7.4.0`_ or higher
-  Graphviz_ (optional)
-  PlantUML_ (optional)

Installing phpDocumentor
------------------------

Using Phive
~~~~~~~~~~~

If phive_ is globally installed, you can run the following command::

   $ phive install phpDocumentor

Once you run the command, phpDocumentor will be installed and it can be executed directly.

For more information about Phive have a look at the `Phive website`_.

By downloading the PHAR
~~~~~~~~~~~~~~~~~~~~~~~

You can download the latest PHAR file from https://phpdoc.org/phpDocumentor.phar or download a specific version from
https://github.com/phpDocumentor/phpDocumentor/releases.

The phar file can be used by invoking PHP directly and providing the phar file as a parameter::

   $ php phpDocumentor.phar -d . -t docs/api

or, on Mac and Linux, you can mark it as executable and move it to your bin folder::

   $ chmod +x phpDocumentor.phar
   $ mv phpDocumentor.phar /usr/local/bin/phpDocumentor

After that you can run it globally::

  $ phpDocumentor -d . -t docs/api

Using Docker
~~~~~~~~~~~~

Although not an actual installation method; the easiest method to use phpDocumentor, if you have Docker installed, is to
use our `Docker image`_. The Docker image comes with all dependencies pre-installed so that you do not have to install
these locally.

To run phpDocumentor using docker, the following should suffice::

    $ docker run --rm -v $(pwd):/data phpdoc/phpdoc:3

And next
--------

- It is recommended to read the :doc:`what-is-a-docblock` section next as it will explain how to quickly start using phpDocumentor.

.. _Docker image:           https://hub.docker.com/r/phpdoc/phpdoc
.. _Composer:               https://getcomposer.org
.. _`PHP 7.4.0`:            https://www.php.net
.. _Graphviz:               https://graphviz.org/download/
.. _PlantUML:               https://plantuml.com/download
.. _Twig:                   https://twig.symfony.com/
.. _Phive website:          https://phar.io/
.. _phive:                  https://phar.io/
