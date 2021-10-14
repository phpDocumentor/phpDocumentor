######
Parser
######

*******
Process
*******

.. uml:: parser/flow.puml
   :width: 100%

************
Message Flow
************

Parsing is powered using Tactician's command bus and has the following messages, in order of handling:

============== ===================================================================================
Name           Description
============== ===================================================================================
LoadCache      Populates the stored file metadata, including a serialized AST.
ParseDirectory Initiates parsing of all files for one Documentation Set, based on a directory
ParseFile      Generate an AST for a single file and adds it to the metadata and Documentation Set
PersistCache   Persists the stored file metadata to disk to be reloaded on the next run
============== ===================================================================================

LoadCache
=========

ParseDirectory
==============

ParseFile
=========

PersistCache
============

**************
Parsing a file
**************

Loading the file's contents
===========================

Creating a Document
===================

As soon as the source file's contents are loaded into a
:php:class:`\phpDocumentor\Guides\RestructuredText\Parser\LinesIterator`, it is time to create our first Node: the
:php:class:`\phpDocumentor\Guides\Nodes\DocumentNode`.

