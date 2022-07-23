#######
Caching
#######

**********************
Please read this first
**********************

By default, phpDocumentor comes pre-configured to run optimally and has caching enabled. This chapter is here to
help you become acquainted with the caching system and how you could tweak it. Thus, if you came here looking how to
make phpDocumentor even faster; it's already on!

Since caching is enabled by default, this document will focus on how to influence the way phpDocumentor caches and the
impact of this.

******************
Where's the cache?
******************

Especially when you integrate phpDocumentor in a Continuous Integration environment, it is useful to know where
phpDocumentor caches to, or even to change where it caches to. This can be helpful when you want to tweak your
pipeline.

The default location is: ``${Current Working Directory}/.phpdoc/cache``.

This will make sure the cache stays with your project. Which is useful when running it using Docker, caching artefacts
in your continuous integration environment and more.

Changing the caching location
=============================

Sometimes, you want control over your cache location. phpDocumentor provides that through the following means:

1. The ``--cache-path`` command-line option, or
2. The ``paths/cache`` key in a configuration file.

To clarify how to integrate it in your configuration, here is an example:

.. code-block:: xml

   <?xml version="1.0" encoding="UTF-8" ?>
   <phpdocumentor ...>
       ...
       <paths>
       	   ...
           <cache>.phpdoc/cache</cache>
       </paths>
       ...
   </phpdocumentor>

Both means support absolute and relative paths.

.. important::

   Relative paths are resolved differently in both methods. When provided to the command-line, it will be relative to
   your current working directory; when present in the configuration file, it is relative to the configuration file's
   location.

********************
How does it help me?
********************

Quick sidebar first: phpDocumentor works by reading _source files_, plucking the juicy bits out and combine that with
a series of templates into a website.

Why is this important to know? So it is easier to understand that phpDocumentor can only cache the first part; where it
reads the source files and plucks the juicy bits from them. It is these juicy bits that we remember for each individual
file.

Thus, when you run phpDocumentor on your source files, it will check whether the file has changed since it saw last
time. Read it if it does and cache that. Combining this information into a website is much harder to cache since it
becomes highly integrated from this point onwards.

Disabling the cache
===================

I'm not sure why you want to, but phpDocumentor makes it possible to disable the cache. Though, sometimes it can be
helpful to disable the cache temporarily if you suspect a caching issue is the cause of unexpected behaviour.

To disable the cache, this is done temporarily by adding the ``--force`` command-line option when running it. But you
can also configure it in your configuration file as a more permanent solution using the ``use-cache`` boolean option:

.. code-block:: xml

   <?xml version="1.0" encoding="UTF-8" ?>
   <phpdocumentor ...>
       ...
       <use-cache>false</use-cache>
       <paths>
           ...
	   </paths>
       ...
   </phpdocumentor>

When is the cache cleared?
==========================

Without passing the ``--force`` command-line argument or manually deleting the contents of the cache folder.
phpDocumentor will decide to clear the cache in any of the following situations.

After upgrading phpDocumentor
-----------------------------

Things change in phpDocumentor, and some of these changes are new to cached information. Without clearing the cache we
would possibly make incorrect assumptions about your code or even throw errors. As such, to ensure that your
documentation is correct phpDocumentor will purge all it's caches when you use a new version of phpDocumentor for the
first time.

After changing the configuration
--------------------------------

To be precise: when the options are changed related to reading and interpreting your source file, that is when the cache
is flushed. The reason for this is that some information is, or should be, discarded when phpDocumentor runs; and this
is stored in the cache. As such, when your configuration changes compared to the previous run, phpDocumentor needs to
do a full analysis on all source files from scratch.
