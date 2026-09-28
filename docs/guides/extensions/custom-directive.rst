#################
Custom directives
#################

.. include:: include.rst.txt

RestructuredText supports `directives <https://docutils.sourceforge.io/docs/ref/rst/directives.html>`_, blocks of the
form ``.. name:: data`` that can be used to extend the markup language with new behavior. phpDocumentor ships with
directives for things like ``.. toctree::`` and ``.. code-block::``, and you can add your own through an extension.

.. note::

    read first :ref:`how to setup<setup-extension>` an phpDocumentor extension before you continue this guide.

In our example we will create a ``.. hello::`` directive that takes a name and greets it, e.g.:

.. code-block:: rst

    .. hello:: World

A directive is a plain PHP class that extends
:php:class:`phpDocumentor\\Guides\\RestructuredText\\Directives\\BaseDirective`. It needs a name, and it turns the
parsed directive into a :doc:`node <custom-node>`:

.. include:: ./examples/Directive/HelloDirective.php
    :code: php

- ``getName()`` returns the name that is used in the RST syntax, in our case ``hello``.
- ``processNode()`` is called by the parser with the parsed :php:class:`phpDocumentor\\Guides\\RestructuredText\\Parser\\Directive`
  (containing the data and options that were written after the directive) and returns the :doc:`node <custom-node>`
  that represents it in the document tree.

.. hint::

    Need access to the raw options that were passed to the directive (``:option: value``)? They are available
    through ``$directive->getOptions()``, ``$directive->getOptionString()``, ``$directive->getOptionBool()`` and
    ``$directive->getOptionInt()``.

Register the directive
-----------------------

Like any other service, the directive needs to be registered in the container and tagged with
``phpdoc.guides.directive`` so that the parser can find it:

.. include:: ./examples/Directive/services.php
    :code: php

Once this is loaded, phpDocumentor will recognise ``.. hello::`` while parsing RestructuredText documents. The next
step is to make sure the node it produces can also be rendered, which is covered in :doc:`custom-node` and
:doc:`node-templates`.
