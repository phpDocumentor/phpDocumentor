File Access
===========

PhpDocumentor uses `FlySystem`_ to access project sources, templates, and write the generated files back to the destination
locations. This allows us to use other locations than just the host filesystem. And doesn't make us bother on
the differences between the different operating systems.

.. note:: In the current version of phpDocumentor, only local filesystems are supported.

Creating filesystems
--------------------

Filesystems used in the application are created by the :php:class:`phpDocumentor\FileSystem\FlySystemFactory` service.
Because of a number of important changes that are made to the handling of :php:class:`phpDocumentor\Dsn` it is required
that all ``League\Flysystem\Filesystem`` objects are created using this factory.

.. _FlySystem: https://flysystem.thephpleague.com/v1/docs/
