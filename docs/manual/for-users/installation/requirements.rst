phpDocumentor has several dependencies on other software packages. Some of
these dependencies are only necessary when generating specific parts of the
documentation, such as Graphs. If a dependency is only
limited to a subset of features it is denoted with the dependency
entry below.

-  `PHP 5.3.3 <http://www.php.net>`_
-  `XSL extension for PHP <http://www.php.net/xsl>`_, only applicable when
   generating HTML via XSL (required)
-  `Graphviz <http://graphviz.org>`_, only applicable when generating Graphs (recommended)

.. warning::

   phpDocumentor can not install these dependencies and will generate errors if
   they are missing.
