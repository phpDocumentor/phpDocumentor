project:run
===========

.. note::

   The contents for this chapter is not complete yet. Why not help us and
   contribute it at
   https://github.com/phpDocumentor/phpDocumentor2/tree/develop/docs/manual


Description
-----------

Usage
-----

::

    $ phpdoc -d|-f <PATH> -t <PATH> [parameters]

::

    $ phpdoc run -d|-f <PATH> -t <PATH> [parameters]

::

    $ phpdoc project:run -d|-f <PATH> -t <PATH> [parameters]

Parameters
----------

**-t**
    The directory in which the generated documentation will be placed.

**-f**
    Provide a single file to parse.

    This parameter can be used to tell phpDocumentor to interpret a single file.
    This may be a single relative or absolute path.
    When providing a relative path please keep in mind that the path is relative
    to the current working directory.

    Wilecards * and ? are not supported by this parameter.

    This parameter may be used in conjunction with the ``-d`` parameter.

**-d**
    Provide a comma-separated list of source folders to parse.

    This parameter can be used to tell phpDocumentor which folders need to be
    interpreted. This may be a single relative or absolute path; or a list of
    paths separated by commas.
    When providing a relative path please keep in mind that the path is relative
    to the current working directory.

    Wildcards * and ? are supported by this parameter but please keep in mind to
    surround the parameter value with double quotes or your operating system
    might try to interpret them instead of phpDocumentor doing so.

    This parameter may be used in conjunction with the ``-f`` parameter.

**--sourcecode**
    *Starting with 0.16.0*, Stores the sourcecode of each file with the structure.

    When this parameter is provided the parser will add a compressed,
    base64-encoded version of the parsed file's source as child element of the
    `<file>` element.
    This information can then be picked up by the transformer to generate a
    syntax highlighted view of the file's source code and even have direct
    links to specific lines.

        Currently the transformer will add a link to the given file's source
        next to the name of the file in the top. In the future we will add
        direct links from elements to the line in this source code file.

**-p|--progressbar**
    While running, show a short summary of generation progress.

    When this parameter is provided the default lengthy command line output is
    replaced with a short overview of the generation status.

    Example:

        Initializing parser and collecting files .. OK
        Parsing files
         80 [============================]                                              
        Storing structure.xml in "/path/to/target/structure.xml" .. OK
        Initializing transformer .. OK
        Processing behaviours .. OK
        Executing transformations
          6 [===========>----------------]    

**--template**
    Specify a template name, or a path to a directory where a custom template resides
