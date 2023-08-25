Installation
============

There are three, recommended, ways to start using phpDocumentor:

1. Pull our pre-built docker_ image (recommended).
2. Use Phive_ to install phpDocumentor as a dependency in your project.
3. Download the PHAR file and place it in the location of your liking.

Which one you use depends on whether you want to use Docker or not, or include
phpDocumentor in your project's setup.

System Requirements
-------------------

If you are using the official Docker container, or Github Action, you do not need to worry about this
and can directly skip to the chapter `Stand-alone, using Docker`_.

phpDocumentor has several dependencies on other software packages. Please make sure that you have these
available before installing phpDocumentor.

- `PHP 8.1.2`_ or higher
- The mbstring_ php extension
- Graphviz_ (optional)
- PlantUML_ (optional)

Stand-alone, using Docker
-------------------------

Although not an actual installation method; the easiest method to use phpDocumentor, if you have Docker installed, is to
use our `Docker image`_. The Docker image comes with all dependencies pre-installed so that you do not have to install
these locally.

To run phpDocumentor using docker, the following should suffice::

    $ docker run --rm -v "$(pwd):/data" "phpdoc/phpdoc:3"

As a convenience, you can alias_ this command so that you can use it from any folder easily::

   $ alias phpdoc="docker run --rm -v $(pwd):/data phpdoc/phpdoc:3"

After doing that, you can simply call ``phpdoc`` from any location. This may also help with following the examples
in this documentation as we assume you have a command called ``phpdoc`` available globally.

As a dependency, using Phive
---------------------------

If phive_ is globally installed, you can run the following command::

   $ phive install phpDocumentor

Once you run the command, phpDocumentor will be installed and it can be executed directly.

For more information about Phive have a look at the `Phive website`_.

Stand-alone, downloading a PHAR
-------------------------------

You can download the latest PHAR file from https://phpdoc.org/phpDocumentor.phar or a specific version from
https://github.com/phpDocumentor/phpDocumentor/releases.

The phar file can be used by invoking PHP directly and providing the phar file as a parameter::

   $ php phpDocumentor.phar -d . -t docs/api

or, on Mac and Linux, you can mark it as executable and move it to your bin folder::

   $ chmod +x phpDocumentor.phar
   $ mv phpDocumentor.phar /usr/local/bin/phpDocumentor

After that you can run it globally::

  $ phpDocumentor -d . -t docs/api

And next
--------

- :doc:`what-is-a-docblock` - how to quickly start using phpDocumentor.
- :doc:`configuration` - advanced configuration options.

.. _Docker image:           https://hub.docker.com/r/phpdoc/phpdoc
.. _Composer:               https://getcomposer.org
.. _`PHP 8.1.2`:            https://www.php.net
.. _Graphviz:               https://graphviz.org/download/
.. _PlantUML:               https://plantuml.com/download
.. _Twig:                   https://twig.symfony.com/
.. _Phive website:          https://phar.io/
.. _phive:                  https://phar.io/
.. _alias:                   https://linuxize.com/post/how-to-create-bash-aliases/
.. _mbstring:               https://www.php.net/manual/en/book.mbstring.php
