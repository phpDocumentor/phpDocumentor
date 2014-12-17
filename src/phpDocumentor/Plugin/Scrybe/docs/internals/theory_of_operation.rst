Theory of Operation
===================

.. warning:: This document is a work in progress

Build cycle
-----------

1. Initialize application

   1. Read configuration file
   2. Find applicable Converter

2. Discovery phase, for each input file:

   1. Build Abstract Syntax Tree (AST) with Converter (does not write files).
   2. Collect meta-data from AST, such as: Document titles, headings, glossary, list of assets

3. Creation phase, for each input file:

   1. Build Abstract Syntax Tree (AST) with Converter
   2. Enhance AST with the discovered meta-data
   3. Generate document in the desired output format
   4. Decorate the results with a template
   5. Save result to disk

   .. note::

      Some output formats do not generate independent files (such as PDF or
      Latex). In this case will step 4 through 5 not be executed for each file
      but for the sum of all files.

4. Copy assets to their destination location
