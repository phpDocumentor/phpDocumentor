Design Document
===============

Goal
----

Our goal with the phpDocumentor documentation is to assist readers in getting running with phpDocumentor within a short
timespan with a minimum of effort. These chapters are designed to become increasingly in-depth, starting with a series
of easy tutorials and how-to's, and ending with references that can be consulted when necessary.

Sections
--------

To assist with a solid structuring for the documentation we have divided documents in the following sections:

Getting Started
  A series of short tutorials that aim to get the user running quickly

Guides
  More in-depth information on how specific concepts work

References
  A pure series of specifications on configuration settings, the full PHPDoc Reference and more.

Developer documentation
  These documents contain architectural overview, technical considerations and other documents that explain how
  phpDocumentor is designed and build. These documents not be visible in the User Manual's table of contents to
  prevent overwhelming the reader.

By making this division we intend to make it easier for new documents to find their way into the documentation and that
it is easier for readers to connect with the flow of the documentation.

Tools
-----

Our user documentation is written using the `RestructuredText`_ markup style and converted to HTML and PDF
using Sphinx_. For more information on installing Sphinx_ we would like to refer you to their website.

.. note::

   In the future we will migrate towards Scrybe_ but given the current state of that project it is currently
   not possible.

For our API Documentation we eat our own dogfood and generate it using phpDocumentor2.

Style Guide
-----------

* All headings should adhere to title case.
* Documents are written in *we*-style; when talking about the project it is we or us and not I.

Outline
-------

.. note::

   This is the original outline as designed on May 30th 2013; it is expected that this will become out of date once
   the documentation becomes stable. This chapter will be removed once setup is complete as it is replaced by the
   table of contents.

::

    Getting Started
    ---------------

    Installing
      System Requirements
      Installing phpDocumentor
        PEAR
        PHAR
        Composer
        Manual Install

    Your First Set of Documentation
      Writing a DocBlock
      Running phpDocumentor

    Changing the Look and Feel
      Applying a Template
      Authoring Your Own Template
        Building on an Existing Template
        Starting from Scratch

    Extending phpDocumentor
      Creating a Service Provider
      Bring Tags to Life
      Write Content the Way You Want

    Guides
    ------

    Anatomy of a DocBlock

    Running phpDocumentor

    Integrating with Your Project
      Configuration
      IDE
      Continuous Integration

    More on Templates

    Service Providers -- Revisited

    Documentation sniffs

    References
    ----------

    PHPDoc Reference
    Tag Reference
    Command Reference
    Configuration Reference

    Developer Documentation
    -----------------------

    Architecture
    Service Providers
    Templates

.. _RestructuredText: http://docutils.sourceforge.net/rst.html
.. _Sphinx:           http://sphinx-doc.org
.. _Scrybe:           http://github.com/phpDocumentor/Scrybe
