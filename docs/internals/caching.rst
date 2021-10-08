Caching
=======

phpDocumentor makes use of caching to speed up repeated runs of the same project. The cache pools are defined in
``/config/packages/framework.yaml`` and are exposed in the service configuration with the following two named services:

1. ``files``, contains cached meta-data for collected files
1. ``descriptors``, contains descriptor objects collected during the previous run

.. important::
   Always explicitly inject cache pools using their names in the service configuration; auto-wiring can
   create a new cache pool and this may cause inadvertent bugs. Such as items not being cache or not being cleared.

Caching process
---------------

In the parsing phase of the application it collects all files that are relevant for processing according to the rules
in the configuration or passed on the command line. When this meta-data has been collected, a determination is made
whether to parse the file again by calculating the md5 sum of the given file and comparing this to the md5 sum present
in the cache.

If the hashes of the current file and the cache do not match, the file is parsed again; otherwise the descriptors that
were cached are re-used.

Clearing the cache or skipping it
---------------------------------

Sometimes you do not want to use the cache or a bug forces you to test without it. By providing the ``--force`` command
line option, the caches are purged before parsing occurs.
