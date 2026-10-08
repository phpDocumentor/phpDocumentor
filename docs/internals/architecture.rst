############
Architecture
############

.. contents::
   :local:
   :depth: 2

Overview
========

phpDocumentor is built from a number of separable subsystems that work together to turn source input into generated
documentation. The system is intentionally divided into layers with clear responsibilities, while still using pipeline
style processing where it helps move data through the application.

This page gives a high-level view of the overall architecture. For a more detailed description of the pipeline stages,
see :doc:`pipeline`.

The main idea is simple: input is collected, parsed into internal structures, enriched and compiled, and then passed to
the transformation layer that produces documentation output. Each subsystem contributes a specific part of that flow
and can be understood on its own before looking at the larger system.

Main building blocks
--------------------

CLI / application bootstrap
~~~~~~~~~~~~~~~~~~~~~~~~~~~

The command-line interface is the user entry point into phpDocumentor. It starts the application, collects user input,
and hands control over to the rest of the system. This layer is responsible for getting the application running and
connecting the user's command to the internal processing flow.

Parsing
~~~~~~~

The parsing subsystem reads the configured source files and turns them into internal structures the rest of the
application can work with. Conceptually, it is the part of the system that understands the input well enough to make it
available to later stages.

Descriptors
~~~~~~~~~~~

Descriptors are phpDocumentor's internal representation of the codebase and related documentation information. They act
as the shared model that other parts of the application use after parsing has extracted the relevant information.

Compiler
~~~~~~~~

The compiler is a distinct architectural part of the system that helps prepare the parsed information for later use.
At a high level, it contributes to combining and organizing the parsed data so the transformation layer can work with a
coherent project model.

Transformations
~~~~~~~~~~~~~~~

The transformation layer turns the prepared internal model into documentation artifacts. Its job is not to discover the
input, but to consume the structured information produced by earlier stages and render it in the form selected by the
current configuration. The detailed rendering steps are described elsewhere in the internals documentation.

Dependency injection
~~~~~~~~~~~~~~~~~~~~

phpDocumentor uses dependency injection to assemble the application from smaller services rather than hard-coding
dependencies between them. This keeps the system flexible and allows individual parts to be replaced or extended while
keeping the overall architecture understandable.

Configuration
~~~~~~~~~~~~~

Configuration controls how the application behaves at a high level. The main configuration files are described in the
internals configuration documentation, and the pipeline documentation explains how those settings are combined during
startup. This page only needs to provide the architectural view: configuration feeds the application setup and shapes
the work done by the later subsystems.

How the pieces relate
----------------------

The architecture is easiest to understand as a combination of layers and data flow.

At the top, the CLI starts the application and passes control into the configured runtime. From there, the system uses
configuration and dependency injection to prepare the services that will handle parsing, compiling, and transforming.

The parsing side of the application gathers and normalizes source input into descriptors. Those descriptors then become
the input for the compiler and transformation layers, which use the internal model to produce the final output.

This split keeps responsibilities separated: the CLI starts the process, configuration and dependency injection prepare
the application, parsing understands input, descriptors represent the project, compilation organizes the model, and
transformations render the result.

Error handling and logging
--------------------------

Error handling and logging support the architecture rather than defining it. They provide the feedback needed when a
command fails, when configuration is incomplete, or when input cannot be processed as expected. At a conceptual level,
they help make the system observable and predictable without changing the responsibilities of the core subsystems.

Guides
------

Guides are part of the broader phpDocumentor ecosystem, and their documentation and implementation can be found under
the guides area and the components that support it. They are mentioned here only as a related part of the application
landscape, not as a focus of this page.

What to read next
-----------------

* :doc:`pipeline` — detailed explanation of the pipeline stages.
* :doc:`configuration` — how application configuration is structured and loaded.
* :doc:`flow` — the overall application flow from the user's perspective.
