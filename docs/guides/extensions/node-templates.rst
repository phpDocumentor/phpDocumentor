###############
Node templates
###############

.. include:: include.rst.txt

Once you have a :doc:`directive <custom-directive>` and a :doc:`node <custom-node>`, the final step is telling
phpDocumentor how to render that node. phpDocumentor uses :doc:`Twig <twig-extension>` templates for this, one
template per node class (and per output format).

.. note::

    read first :ref:`how to setup<setup-extension>` an phpDocumentor extension, and :doc:`custom-directive` and
    :doc:`custom-node` before you continue this guide.

First we write a Twig template for our ``HelloNode``. Inside the template, ``node`` refers to the node instance, so
any public getter on the node (like ``getName()``) is available as ``node.name``:

.. include:: ./examples/Directive/hello.html.twig
    :code: twig

Register the template
----------------------

Templates are registered in your extension's ``Extension`` class rather than through ``services.php``, because two
container parameters need to be updated: the list of directories Twig should look for templates in
(``phpdoc.guides.base_template_paths``), and the mapping from node class to template file
(``phpdoc.guides.node_templates``):

.. include:: ./examples/Directive/Extension.php
    :code: php

The ``template()`` helper builds the array structure phpDocumentor expects for a template mapping: which node class
it applies to, which template file to use, and, optionally, for which output format (``html`` by default).

With the template registered, ``.. hello:: World`` will render as ``Hello, World!`` wherever it is used in your
documentation.
