Routing
=======

Introduction
------------

Template builders should have the freedom to choose any URL structure that they desire. As such phpDocumentor should
facilitate in a component that allows them to generate routes in templates for Structural Elements; as to be able
to link to other parts of the documentation.

Goals
~~~~~

The following goals have been defined as functional parameters to enable routing,

#. Map Structural Elements to a path relative to the destination location's root, or a well-formed URL.
#. Map specific Structural Elements to different routes, based on their Fully Qualified Structural Element Name or parts
   thereof.
#. Prevent documentation files from being generated, useful for included libraries.
#. Allow multiple routers to be applied.

With these goals it is possible to allow for a fine-grained routing system where the user can decide to separate
namespaces into separate component directories, or even to link to external documentation by routing a name to an
external URL.

Process
-------

The process of routing can be executed in two different locations:

#. In a template file.
#. For a transformation declaration in the template definition file.

.. note::

    Routing support is provided by a writer, as such it is possible that this functionality is not available everywhere.
    The documentation of a writer should indicate whether that writer supports the routing mechanism.

By using routes in both of the above locations will phpDocumentor know exactly where to generate files and how to link
individual parts of the documentation to other parts.

In a template file
~~~~~~~~~~~~~~~~~~

#. A signal in a template indicates the use of a route; for twig that will be a filter called ``route``.
#. All declared routers will be called in sequence of priority with the value of the given *node*.

   .. important:: this may be an object or string

#. the first router will try to find a *match* for the given *node*.
#. if not found, go to the next router.
#. if a *match* is found with a specific *route* in the router then *generate* a url according to the
   *scheme* that is associated with that *route*.
#. The generated URL is returned and a link is created pointing to that URL

.. important::

   Every writer may have their own mechanism for calling custom pieces of code. Please consult the
   documentation of your desired writer to learn how to apply routes.

In the template definition
~~~~~~~~~~~~~~~~~~~~~~~~~~

#. if a transformation lacks an ``artifact`` attribute then the router will be invoked by the writer
#. All declared routers will be called in sequence of priority with the value of the given *node*.

   .. important:: this may be an object or string

#. the first router will try to find a *match* for the given *node*.
#. if not found, go to the next router.
#. if a *match* is found with a specific *route* in the router then *generate* a url according to the
   *scheme* that is associated with that *route*.
#. the writer will use the generated url if it is relative; in case of an absolute URL or the value false will no
   template be generated.

.. important::

   Only writers that support the router can be used in this way. Please consult the
   documentation of your desired writer to learn how to apply routes.

Example scenario
~~~~~~~~~~~~~~~~

In a template definition is a transformation to generate artifacts for all classes in a project using Twig templates.
When I omit the artifact attribute the Twig writer will invoke the *router* for each class that it processes and pass
an object of the class ``ClassDescriptor``.

The router will iterate through every *Routing Rule* and try to match the ClassDescriptor to that *rule*.

When a *matching* *rule* is found (such as: for every ClassDescriptor), then the ClassDescriptor is passed to the
*UrlGenerator*. The *UrlGenerator* will subsequently try to convert the given object to an URL (as string) and return
that back to the writer.

The writer will determine whether the returned URL is relative. If the returned URL is absolute or the boolean value
false, than the writer must not generate an artifact.

If the provided URL is relative then the writer will generate an artifact at the given URL. During generation the writer
will check for special markers or indicators that a node in the template should be converted to a link to another part
of the application.

If such a marker is found, such as the ``route`` filter in the Twig writer, then the writer will iterate through each
*Routing Rule* and try to match that *node*.

When a *matching* *rule* is found, then the node is passed to the *UrlGenerator*. The *UrlGenerator* will subsequently
try to convert the given object to an URL (as string) and return that back to the writer.

If the returned URL contains the boolean value false, than the writer must not generate a link and ignore the
directive, otherwise the writer will generate a link pointing to the given URL.

Components
----------

Node
    The element that is passed to a router, this may be a string or a child class of DescriptorAbstract.

Router
    A container that houses a series of logically connected Routing Rules

Routing Rules (Routes)
    The relation between a Matcher and a UrlGenerator; represents the conversion of a single Node to a URL

Matcher
    A component that is responsible for determining whether the given Node triggers the parent Routing Rule.

UrlGenerator
    A component that is responsible for converting a given Node to a URL representation.

Defining routers
----------------

By default phpDocumentor imposes its default routers on a template, so unless you want to configure your own
routers you can ignore the following section.

If you want to ship your own routers then they can be defined by adding ``<router>`` elements to your application
configuration. Each router element should have a priority attribute that indicates where in the execution order the
router should be placed.

Priorities
----------

TODO
----

* Show router queue using a command
* Define routes inline in a template
* How to handle destination types? How does a writer know what type of destination we are dealing with?