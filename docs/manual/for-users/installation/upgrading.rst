Upgrading
=========

How to upgrade phpDocumentor depends on your chosen installation method.

Manual installation
-------------------

* Overwrite your existing ``installer.php`` in the phpDocumentor source folder
  with a new version of the installer located at
  https://raw.github.com/phpDocumentor/phpDocumentor2/develop/installer.php.
* Execute the installer; it will download and install the latest version of
  phpDocumentor.

.. note::

   The installer does not remove your existing installation; this means that
   any files that were deleted between versions will remain. It is recommended
   to remove your existing installation first, if possible.

Phar
----

Download a new phar archive from http://phpdoc.org/phpDocumentor.phar.

PEAR
----

Upgrading your installation using PEAR can be done using the following command::

    $ pear upgrade phpdoc/phpDocumentor-alpha
