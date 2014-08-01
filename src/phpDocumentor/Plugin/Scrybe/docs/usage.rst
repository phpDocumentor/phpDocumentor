Usage
=====

Installation
------------

Installing is a matter of downloading the scrybe.phar that is available at:

    http://www.phpdoc.org/scrybe.phar

And after that you could view what tasks are available by entering::

    $ php scrybe.phar

or::

    $ php scrybe.phar list

That exposes which output formats are currently supported. This list will be
expanded in the future when more features are implemented.

Upgrading
---------

Upgrading your installation of Scrybe is a matter of running the following
command::

    $ php scrybe.phar update

.. note:: This command is currently not shown in the command listing.

Running
-------

Scrybe is easy to use, for each output format there is a separate task.
These tasks may have specific properties so it pays to review it with the
``help`` command like this::

    $ php scrybe.phar help [command]

New commands are added as this project progresses to be sure to regularly check
this  document.

Converting to HTML
~~~~~~~~~~~~~~~~~~

To convert your documentation to HTML you can run the following command::

    $ php scrybe.phar manual:to-html [SOURCES] [...]

By default it will output your documentation to a folder *build* and use
*RestructuredText* as input format.

To change this you can make use of the following command line options::

    --target (-t)       target location for output (default: 'build')
    --input-format (-i) which input format does the documentation sources
                        have? (default: 'rst')
    --title             The title of this document (default: 'Scrybe')
    --template          which template should be used to generate the
                        documentation? (default: 'default')

Thus, for example: to output your documentation from the docs folder to the
*web* folder you can use the  following command::

    $ php scrybe.phar -t web docs

Scrybe offers support for changing the look & feel using Twig templates.
To change a template you only need to provide the name of a subfolder of
the folder ``data/templates`` in Scrybe using the --template option.

For example::

    $ php scrybe.phar --template default docs

The default is, of course, *default*. Thus using ``--template`` in the above
example is overkill but illustrates the point.

.. note::

   Scrybe uses the template name to look in the ``data/templates/[TEMPLATE]``
   folder for a file called ``layout.html.twig`` and uses the *content* variable
   to insert the generated HTML contents.