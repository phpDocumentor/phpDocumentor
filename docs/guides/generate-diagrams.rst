Generate diagrams
=================

phpDocumentor can generate diagrams for your code. This can be useful to get a better understanding of the structure
of your codebase. The diagrams are generated using `PlantUML <http://plantuml.com/>`_. When you run :ref:`phpDocumentor using
docker <Stand-alone, using Docker>`, the PlantUML binary is included in the Docker image.
If you are using the phar installation you need to install PlantUML separately.

Install plantuml (optional)
---------------------------

.. note::

    This option is supported since phpDocumentor 3.7.0

PlantUML is a Java application that can be installed on your system. You can download the latest version from the
`PlantUML website <https://plantuml.com/download>`_.

When you installed plantuml you can set the path to platuml as an environment variable::

    $ export PHPDOC_PLANTUML_BIN=/path/to/plantuml

Use plantuml server (optional)
------------------------------

.. note::

    This option is supported since phpDocumentor 3.7.0

PlantUML also provides a server that can be used to generate diagrams. Right now, the server usage is limiting the size
of your diagrams. Large diagrams will not be generated. You can set the server URL as an environment variable::

    $ export PHPDOC_PLANTUML_SERVER=http://www.plantuml.com/plantuml/svg/
    $ export PHPDOC_PLANTUML=plantuml-server

The second environment variable is to switch to the server mode. If you do not set this variable, phpDocumentor will
use the local PlantUML binary.

