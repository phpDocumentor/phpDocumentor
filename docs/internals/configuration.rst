Configuration
=============

Introduction
------------

phpDocumentor's configuration module provides the application with the ability to load configuration from disk and
augments that with the information provided by the user when they started phpDocumentor.

In doing this it populates an entity of class :php:class:`phpDocumentor\Configuration\Configuration` and adjusts this
when newer configuration options become available.

.. note::
    this is a mutable object that may change during the life cycle of the application by design.

The configuration object is not meant to replace parameters in the dependency injection container or internal variables,
for this phpDocumentor has an Dependency Injection Container. The configuration object is meant for all options and
parameters that users of the application may need to consume. Examples of such options is where to write the
documentation to (target) or the list of files that it should parse.

In the appendix of this chapter an overview of the configuration structure is given, including a description of the
function of each element and section.

Consuming configuration in services
-----------------------------------

There are two ways of consuming configuration options in your services:

1. Use a pipeline stage to execute your logical action and read the configuration from the payload in that stage
2. Inject the Configuration object into your service.

.. important::
   Configuration options cannot be injected in the symfony configuration (services.yml, etc) because the
   configuration files are loaded on run-time, after the symfony service configuration has happened.

Pipeline stages
~~~~~~~~~~~~~~~

    This is the recommended method as it decouples your service from the configuration object, making it easier to make
    changes to either class in the future.

The backbone of phpDocumentor is a pipeline with a series of stages; each stage receives a Payload object that, among
other things, contains a configuration array. This configuration array contains the latest version of the configuration
and the command line options merged into it.

In your stage you can read this array to grab the options you need for your service and inject them on run time.

Injecting the configuration object
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

When using the auto-wiring options of the Dependency Injection Container (which are the default in phpDocumentor) you
can inject the configuration object by defining a constructor argument with the typehint
``phpDocumentor\Configuration\Configuration``.

This is a straightforward way to receive and read the configuration but has several downsides, which is why using a
Configurator is recommended:

- The Configuration object is mutable: by design the Configuration object is mutable and this means that any change
  you make to it in your service will be picked up in subsequent calls to the Configuration object and this may
  cause unintended side-effects in unexpected places if you are not careful.
- Your service needs to have knowledge how the configuration is structured, making it harder to change the structure
  of the configuration. The same could be said of Configurator services, however: in those services it is expected and
  easier to discover as containing information on the structure of the configuration.

How is the configuration read?
------------------------------

The **Configure** stage is responsible for loading the configuration files using the **ConfigurationFactory**. This
factory supports versioning of configuration files by reading the ``configVersion`` from the root of the XML configuration
file and finding the appropriate Configuration Definition.

.. hint:: If no configVersion is provided, the application assumes version 2.

When a Configuration Definition is found, the loaded XML file is processed by it and normalized afterwards. You can add
more advanced normalization by implementing the Normalizable interface in your definition and adding rules in your
*normalize* method that will transform your configuration array.

At this point, the ConfigurationFactory determines if the ``configVersion`` is the latest version known by phpDocumentor.
This can defined in the service definition for the ConfigurationFactory, the last version in the array of definitions is
considered the last.

If the configVersion is insufficient, then the ConfigurationFactory will try to upgrade the generated configuration
array to the latest version by using the upgrade method of the 'old' definition. This will generate a new input for
another configuration definition and the process starts all over again. This will loop until no newer configuration is
found.

Adding a new version of the configuration
-----------------------------------------

1. Create a new Configuration Definition in ``src/phpDocumentor/Configuration/Definition`` and write the configuration
   definition using the TreeBuilder (see existing Definitions for examples).

2. Add the new version to the array of definitions in the service container configuration for the SymfonyConfigFactory

3. Implement the Upgradable interface in the second to highest definition and convert the generated array of that
   definition to the input array for the new one, including the appropriate configVersion set to the latest version
   number. This is how the configuration system knows how to parse this new input.

   The input array differ from the output as the configuration system normalizes the input to something that is easily
   consumed in the rest of the application; when in doubt: add a debug statement in the SymfonyConfigFactory after the
   loading of the XML file; this is the format you need to emulate in the upgrade method of the prior Definition.

.. hint::

   It is recommended to also write an XSD and put that in the ``data/xsd`` folder; see the Version 3
   configuration for an example.
