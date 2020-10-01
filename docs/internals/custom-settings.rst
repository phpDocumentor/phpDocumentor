Custom Settings
===============

phpDocumentor supports a mechanism to provide free-form settings from the command line using the ``--setting`` command
line argument. This is primarily used by Writers who could use extra configuration or when we want to use feature flags
to toggle experimental features.

Defining custom settings
------------------------

To define custom settings, a writer needs to extend the WithCustomSettings interface. This interface will tag the
writer as supporting custom settings and will allows the ``./bin/phpdoc list:settings`` command to return an overview
of its custom settings.

The WithCustomSettings interface will request the Writer to provide all supported settings, with their default values,
using the ``getDefaultSettings`` method.

Usage
-----

A writer can request its custom settings through the ProjectDescriptor using the following method chain:

``$project->getSettings()->getCustom()``

This chain will return an array containing all custom settings, including those of other writers, from which the writer
can retrieve their own settings.

Booleans
--------

When the value is a string 'true' or 'false', this will automatically be converted to its boolean counterpart. This is
done to prevent mistakes when programming as the strings true and false are not equivalent to the boolean values true
and false.

Effects on caching
------------------

The ``settings`` in the ProjectDescriptor is being watched by phpDocumentor; any change within that structure will
force phpDocumentor to purge its cache because it will influence the parsing process. This means then whenever you pass,
or omit, a custom setting for the first time; the cache will be flushed.
