Documentation
=============

Introduction
------------

This document intends to describe the concept and structure behind the
phpDocumentor 2 documentation and how to generate the HTML version of the
documentation.

Writing
-------

Documentation at phpDocumentor2 is written with the following mission statement:

    *Write documentation for the users, as you would want your users to write
    documentation for you*

This means that we want documentation that is clear, concise and conveys the
right kind of information for the reader.

To reach this goal the documentation has been split into 3 distinct manuals:

1. For users of phpDocumentor
2. For template builders on phpDocumentor
3. For developers on phpDocumentor

The first manual aims at developers who are interested in using the application
and finding out how it works.

The second manual aims at developers who are interested in writing templates and
expanding the library of templates of phpDocumentor.

The third manual aims at developers who want to contribute to phpDocumentor or
want to know how it works. This last manual also writes down the development
procedures and how common tasks can be executed (such as generating this
documentation).

Generating
----------

Installing Sphinx
~~~~~~~~~~~~~~~~~

The intention is to use Scrybe_ once that has matured enough to be usable for
generating large pieces of documentation; until that time we use Sphinx_ 1.1.3+.

To use Sphinx_ the following needs to be installed on your system (the package
names given are those of ``aptitude``; other operating systems are not described
in this document):

- graphviz
- python-pip
- texlive-latex-extra
- texlive-latex-recommended

After making sure the above packages have been installed you can use the
command to install Sphinx_ itself::

    sudo easy_install sphinx

GraphViz_ is used for PlantUML_, with which you can generate UML diagrams in
the documentation.

.. note::

   Use Python's easy_install utility instead of a apt package to obtain the
   latest version.

Running
~~~~~~~

The ``/docs/manual`` contains a *Makefile* that can be used to generate the
documentation using the following command::

    make html

The generated HTML is put in the ``/docs/manual/.build/html`` folder.

Should you wish to create a set of PDF files you can use the following command::

    make latexpdf

This command will run latex and create 3 PDFs in the
``/docs/manual/.build/latex`` folder:

1. phpDocumentor.pdf
2. phpDocumentor-for-template-builders.pdf
3. phpDocumentor-for-developers.pdf

Each PDF matches the subcategorization as described in the chapter `Writing`_.

Updating the online documentation
---------------------------------

To update the online documentation you will need to the project's root folder
and execute the following command::

    phing deploy:update-manual

This command will generate the documentation and upload it to the correct
location as described in the ``build.properties`` file.

.. _Scrybe:   https://github.com/phpDocumentor/Scrybe
.. _PlantUML: http://plantuml.sourceforge.net/
.. _Sphinx:   http://sphinx.pocoo.org/
.. _GraphViz: http://www.graphviz.org/