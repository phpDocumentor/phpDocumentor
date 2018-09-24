Configuration
=============

Introduction
------------

phpDocumentor's configuration module provides the application with the ability to load configuration from disk and
augments that with the information provided by the user when they started phpDocumentor.

In doing this it populates an entity of class `phpDocumentor\Configuration\Configuration` and adjusts this when newer
configuration options become available.

    Please note: this is a mutable object that may change during the life cycle of the application by design.

The configuration object is not meant to replace parameters in the dependency injection container or internal variables,
for this phpDocumentor has an Dependency Injection Container. The configuration object is meant for all options and
parameters that users of the application may need to consume. Examples of such options is where to write the
documentation to (target) or the list of files that it should parse.

In the appendix of this chapter an overview of the configuration structure is given, including a description of the
function of each element and section.

Consuming configuration in services
-----------------------------------

There are two ways of consuming configuration options in your services:

1. Create a Configurator service (https://symfony.com/doc/current/service_container/configurators.html)
   and use that to inject configuration options into your service.
2. Inject the Configuration object into your service.

Configurator services
~~~~~~~~~~~~~~~~~~~~~

    This is the recommended method as it decouples your service from the configuration object, making it easier to make
    changes to either class in the future.

A Configurator forms a bridge between your service and the configuration. In your configurator you can read the parts of
the configuration that are relevant for your service and, for example, use setters or similar methods to inject the
configuration into your service.

    Example: The `phpDocumentor\Parser\Parser` contains various options with which the process of creating
    documentation can be influenced, such as the setDefaultPackageName method. A Configurator can inject the
    default-package-name configuration option into the Parser by calling this method.

Injecting the configuration object
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

When using the auto-wiring options of the Dependency Injection Container (which are the default in phpDocumentor) you
can inject the configuration object by defining a constructor argument with the typehint
`phpDocumentor\Configuration\Configuration`.

This is a straightforward way to receive and read the configuration but has several downsides, which is why using a
Configurator is recommended:

- The Configuration object is mutable: by design the Configuration object is mutable and this means that any change
  you make to it in your service will be picked up in subsequent calls to the Configuration object and this may
  cause unintended side-effects in unexpected places if you are not careful.
- Your service needs to have knowledge how the configuration is structured, making it harder to change the structure
  of the configuration. The same could be said of Configurator services, however: in those services it is expected and
  easier to discover as containing information on the structure of the configuration.
