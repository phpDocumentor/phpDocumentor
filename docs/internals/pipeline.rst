############
The Pipeline
############

Building on top of the document on the :doc:`application flow<flow>`, let's go one step further and describe how that
process is reflected in phpDocumentor's architecture.

phpDocumentor uses `the Pipeline component of the League of Extraordinary Packages`_ to make a sequential series of
steps, called *Stages*, to transform the provided configuration into a ProjectDescriptor, which in turn is used as the
basis for creating artifacts containing information from that Descriptor.

.. hint::

   In phpDocumentor, each node in the AST is called a Descriptor. Where AST nodes provide basic information,
   phpDocumentor layers more information on top of that based on the complete project.

To be more precise, we define multiple of these pipelines in the configuration file ``config/pipelines.yaml``. We
distinguish between these main pipelines:

#. The configuration pipeline (``phpdoc.configuration.pipeline``)
#. The parser's pipeline (``phpdoc.parse.pipeline``)
#. The transformer's pipeline (``phpdoc.transform.pipeline``)

And each of these pipelines are *stages* in the application's main pipeline: ``phpdoc.complete.pipeline``.

********************
Composable pipelines
********************

As an aside, let's reflect on why we use pipelines. phpDocumentor's architecture is built on top of the principle of
making it easy to extend. To allow developers to influence how phpDocumentor works, we took the Symfony Service
Container's tagging functionality, and created a special factory class.

The service container will look for any pipeline or stage with the right tag and pass these onto a pipeline builder in
order of the tag's priority. See the Symfony documentation for more information on
`tagging <https://symfony.com/doc/current/service_container/tags.html>`_.

The main stage's priorities are all multiples of 1000 to make sure there is plenty of room in between these priorities
to add one's own stages. The order of these priorities go from positive MAX_INT to negative MAX_INT (thus: descending)
where priority 0 represents the main Stage for each pipeline, for example: the ``ParseFiles`` stage for the Parser.

And now, back to the main topic!

**************
Main pipelines
**************

Each pipeline has a specific responsibility and can transform its input into another type of input during its lifetime.

Configuration pipeline
======================

This pipeline takes the provided command-line options, merges it with the configuration files and with the application
defaults; and with this it creates the Payload for the Parser pipeline.

**Input**
    Array with provided command line options.

**Output**
    :php:class:`Payload<phpDocumentor\Pipeline\Stage\Payload>` with the loaded configuration and a
    :php:class:`ProjectDescriptorBuilder<phpDocumentor\Descriptor\ProjectDescriptorBuilder>`.

The configuration phase of phpDocumentor is focused on preparing the application to parse the documentation sets as
configured by the end-user.

As of writing, the main stages in this pipeline are:

#. :php:class:`Configuring the application<phpDocumentor\Pipeline\Stage\Configure>` - merging the command line options
   with the configuration file and application defaults to get the complete configuration for the upcoming parsing and
   transforming phases.
#. :php:class:`Creating the payload<phpDocumentor\Pipeline\Stage\TransformToPayload>`
#. :php:class:`Preparing the ProjectDescriptor<phpDocumentor\Pipeline\Stage\InitializeBuilderFromConfig>` - the
   ProjectDescriptor should be prepared with the basic meta-data coming from the configuration.

   This includes information such as the name, source and output locations, and which versions and components to
   create documentation for.
#. :php:class:`Purging caches<phpDocumentor\Pipeline\Stage\Cache\PurgeCachesWhenForced>` - when the ``--force`` option
   is provided, or the cache is disabled in the configuration, this stage will make sure the cache is purged.

Parser pipeline
===============

**Input**
    :php:class:`Payload<phpDocumentor\Pipeline\Stage\Payload>` with the loaded configuration and a
    :php:class:`ProjectDescriptorBuilder<phpDocumentor\Descriptor\ProjectDescriptorBuilder>`.

**Output**
    :php:class:`Payload<phpDocumentor\Pipeline\Stage\ParserPayload>` with the loaded configuration and a
    :php:class:`ProjectDescriptorBuilder<phpDocumentor\Descriptor\ProjectDescriptorBuilder>`.

As of writing, the main stages in this pipeline are:

#. :php:class:`phpDocumentor\Pipeline\Stage\Parser\TransformToParserPayload`
#. :php:class:`phpDocumentor\Pipeline\Stage\Parser\CollectFiles`
#. :php:class:`phpDocumentor\Pipeline\Stage\Cache\GarbageCollectCache`
#. :php:class:`phpDocumentor\Pipeline\Stage\Cache\LoadProjectDescriptorFromCache`
#. :php:class:`phpDocumentor\Pipeline\Stage\Parser\ParseFiles`
#. :php:class:`phpDocumentor\Pipeline\Stage\Cache\StoreProjectDescriptorToCache`

Transformer pipeline
====================

**Input**
    :php:class:`Payload<phpDocumentor\Pipeline\Stage\Payload>` with the loaded configuration and a
    :php:class:`ProjectDescriptorBuilder<phpDocumentor\Descriptor\ProjectDescriptorBuilder>`.

**Output**
    :php:class:`Payload<phpDocumentor\Pipeline\Stage\ParserPayload>` with the loaded configuration and a
    :php:class:`ProjectDescriptorBuilder<phpDocumentor\Descriptor\ProjectDescriptorBuilder>`.

As of writing, the main stages in this pipeline are:

#. :php:class:`phpDocumentor\Pipeline\Stage\Cache\LoadProjectDescriptorFromCache`
#. :php:class:`phpDocumentor\Pipeline\Stage\Compile`
#. :php:class:`phpDocumentor\Pipeline\Stage\Transform`
