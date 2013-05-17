Architecture
============

.. image:: /.static/for-developers/architecture/overview.png
   :align: center

Introduction
------------

phpDocumentor uses the concept of `modular programming`_ to achieve a loose and interchangeable architecture. This
practice emphasizes that the business logic is split into reusable components that may be swapped or expanded with
minimal effort.

The architecture of phpDocumentor is divided into 4 separate layers:

.. toctree::

   Application layer, glues the application together using Dependency Injection (DI) <application/index>
   Parser layer, analyzes a project and provides a static analysis <parser/index>
   Descriptors, provides a light-weight, and cacheable, representation of a project <descriptors/index>
   Transformer layer, uses Descriptors to generate the artifacts (output files) <transformer/index>

In this architecture the phpDocumentor Application layer acts as glue to combine its three main parts; the Parser,
Descriptors and Transformer.

More specifics on each of the application phases can be found in the following chapters,

Application flow
----------------

Below is a simplified representation of the application's flow. Some of the underlined terms can be found in
the :doc:`glossary <../../for-users/introduction/definitions>` or elsewhere in these chapters.

.. uml::
   skinparam activity {
       backgroundColor #f0f0f0
       borderColor #c0c0c0
       shadowing false
       arrowColor #c0c0c0
   }

   start

   :User invokes command line\ncommand <b>run</b>;

   :Application starts;

   fork

      :<u>Parser</u> parses project source\nfiles;

   endfork

   :Element <u>Descriptors</u> are created\nwith parsed data;

   :<u>Descriptors</u> are validated;

   :<u>Descriptors</u> are cached;

   fork

      :<u>Compiler steps</u> enrich <u>Descriptors</u>;

      :<u>Templates</u> are loaded;

      :<u>Transformations</u> in <u>templates</u>\nuse <u>Writers</u> to create <u>artifacts</u>;
   endfork

   stop

.. _`modular programming`: http://en.wikipedia.org/wiki/Modular_programming
