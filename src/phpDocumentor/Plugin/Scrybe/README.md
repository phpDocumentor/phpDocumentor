Scrybe
======

The Scrybe plugin for phpDocumentor allows you to generate manuals and other hand-made reference materials by combining
a markup language and a template. Currently this plugin can generate HTML documentation but support for PDF and LaTeX is
planned.

Usage
-----

To use this plugin, all you need to do is invoke the correct command::

    $ ./bin/phpdoc.php manual:to-html -t build/docs src

The above command will generate HTML documentation from the markup files in the `src` folder into the `build/docs`
folder.
