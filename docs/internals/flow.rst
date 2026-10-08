################
Application Flow
################

************
Introduction
************

Generating the documentation for a project involves a fair number of steps but can be best summarized with this
Activity Diagram.

.. uml:: Main program flow
   :classes: float-right

   start
   :Boot the application;
   :Parse files into an AST;
   :Transform AST into artifacts;
   stop

This three-step process shows the overall path from starting the application to producing documentation output.

An example of such output may be a website that documents the project's internal API. Another example could be a
Checkstyle XML document that describes which errors were found in the project's DocBlocks.

***********************
The flow in more detail
***********************

The complete application flow is best described by the Activity Diagram below. It covers the main activities that occur
in the application. The diagram stays at a high level and does not include every internal detail.

Another thing to note is that Symfony dependency injection is not shown explicitly in this flow. It is part of the
application setup and wiring, but the diagram focuses on the user-visible stages of the run.

The sections below provide more detail on the individual activities shown in the diagram, including the ones that are
collapsed there for readability.

.. uml:: flow.puml

Boot the Application
====================

.. uml::

   :Initialize dependencies using Application;
   :Load configuration;
   :Add logging;
   :Register Symfony services;

Parse files into an AST
=======================

.. note::

    The following activity diagram is an excerpt from the diagram at the beginning of the chapter and is repeated here
    to support the text.

.. uml::

   :Set parsing parameters;
   :Find project files;
    :Load descriptor cache;
    :Remove stale items from descriptor cache;

   while (There are unprocessed files?) is (Yes)
       if (File is cached and cache is valid) then (Yes)
           :Load Cached File;
       else (No)
           :Add File Representation to Project|
       endif;
   endwhile (No);

    :Write partial text to project;
    :Save cache to disk;

To generate documentation properly, phpDocumentor needs to find all files in the project that should be documented.
Several options influence which files are eligible, such as source directories and the list of ignored files.

If the target folder contains a cache from a previous run, phpDocumentor loads it and removes entries that no longer
match the current file list.

Once that is done, phpDocumentor has a description of the project, represented by an instance of the
ProjectDescriptor class. It may already contain descriptors that were discovered during a previous run.

When phpDocumentor is ready to create or refresh the AST, it iterates over all discovered files. A hash is generated
for each file and checked against the cache to determine whether the file is still *fresh*. If the hash is missing or
different, phpDocumentor creates a new representation of that file.

.. important::

    At this stage, links between elements are still stored as strings. They are turned into actual references later in
    the process.

    This is done because:

   - caching references to objects can easily disconnect the two objects
   - if a file is refreshed then all links are lost and should be re-made
   - filtering and alterations may be done at later stages and actual references may become stale or new ones should
     be made.

Add File Representation to Project
----------------------------------

.. uml::

   start

   :Reflect file;
    :Create file representation as FileDescriptor;

   while (For each Structural Element in File)
        :Map reflected information onto new descriptor;
        :Filter Descriptor;
        :Validate Descriptor;
        :Add element descriptor to file;
   endwhile;

    :Add file representation to project;

   stop

Transform AST into artifacts
============================

Transform all files
-------------------

.. uml::

   start

    #f9f9f9:Emit event "transformer.transform.pre"> 
    #f9f9f9:Emit event "transformer.writer.initialization.pre">
    :Boot involved writers;
    #f9f9f9:Emit event "transformer.writer.initialization.post">

   while (For each Transformation)
       #f9f9f9:Emit event "transformer.transformation.pre">
       :Execute associated Writer and pass Transformation;
       #f9f9f9:Emit event "transformer.transformation.pre">
   endwhile;

    #f9f9f9:Emit event "transformer.transform.post">

    stop

The transformation step is where phpDocumentor turns the prepared project model into the final output.

Taken together, the steps in this chapter show how phpDocumentor moves from a command-line invocation to rendered
documentation output.
