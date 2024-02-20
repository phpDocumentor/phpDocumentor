==========
Directives
==========

phpDocumentor has a number of directives that can be used to add more dynamic content to your documentation. These
directives are used to add information from the source code to the documentation, such as the description of a class,
the list of methods and properties, and so on. As the list of classes and methods will change over time these
directives are a great way to keep your documentation up to date.

.. note::

    Currently directives are only supported in reStructuredText as commonmark does not support the necessary syntax extensions.
    We are investigating the possibility of adding support for commonmark in the future.

Class Diagram
=============

The class diagram directive is used to add a class diagram to your documentation. The class diagram is generated
from the source code and shows the relationships between classes, interfaces, and traits.

The class diagram directive uses our own :ref:`query language` to select the classes to include in the diagram.

This example will include a class diagram with all classes, interfaces, and traits in the
:php:namespace:`\phpDocumentor\Descriptor` namespace.

.. code-block:: rst

    .. phpdoc:class-diagram:: [?(@.namespace starts_with "\phpDocumentor\Descriptor")]
