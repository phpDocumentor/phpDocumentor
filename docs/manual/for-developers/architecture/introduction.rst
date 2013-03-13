Introduction
============

This chapter provides an overview of the architecture of phpDocumentor and how each component interacts.

phpDocumentor uses the concept of `modular programming`_. This practice emphasizes that the business logic is split into
reusable components.

The Component Diagram below shows the interaction between the different components and that they are glued together
using the Application component (more specifically, the ``phpDocumentor\Application`` class).

.. uml::

   [Application] -left-> [Cilex]
   [Application] -up-> [Service Providers]
   [Application] -right-> [Descriptor]

   package "Parsing" {
     [Parser] -down-> [Reflection]
     [Reflection] -down-> [ReflectionDocBlock]
   }

   package "Compiling" {
       [Compiler] -down-> [Linker]
       [Compiler] -down-> [Compiler passes]
       [Compiler] -down-> [Transformer]
   }

   [Application] -down-> [Parser]
   [Application] -down-> [Compiler]

   [Parser] .> [Descriptor]
   [Descriptor] ..> [Compiler]
   [Cilex] <.. [Service Providers]

These components fulfill the following roles:

====================== =========================================================================================
Name                   Description
====================== =========================================================================================
Application            Component representing the application itself, wires all other components.
— Cilex_               Command Line Application light-weight Framework.
— Service Providers    Cilex_ service providers are leveraged to provide plugins for phpDocumentor.
Descriptor             Light-weight objects providing an OO description of the structure of a project.
Parser                 Finds all files the need to be reflected, reflects those and builds a Project Descriptor.
— Reflection           Breaks down the source of a single PHP file into elements using Static Reflection.
—— ReflectionDocBlock  Breaks down a DocBlock to its bare elements.
Compiler               Processes the built Project Descriptor using a series of compiler passes
— Linker               Compiler pass used to generate link Fields and Descriptors (such as a parent class).
— Compiler passes      Allows the system, and Service Providers, to add additional behaviour to the system.
— Transformer          Transforms the Project Descriptor into a (series of) artifacts(s).
====================== =========================================================================================

Details on these components (except for Cilex_) are described in subsequent chapters.

.. _`modular programming`: http://en.wikipedia.org/wiki/Modular_programming
.. _Cilex:                 http://cilex.github.com
