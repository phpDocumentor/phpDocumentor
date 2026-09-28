############
Custom nodes
############

.. include:: include.rst.txt

A directive does not render anything by itself: it only describes *what* should end up in the document, as a
:php:interface:`phpDocumentor\\Guides\\Nodes\\Node`. Rendering that node into HTML (or any other output format) is a
separate step, handled by a Twig template as described in :doc:`node-templates`.

This separation keeps parsing and rendering independent from each other, so the same node can be rendered
differently per output format without having to touch the directive at all.

.. note::

    read first :ref:`how to setup<setup-extension>` an phpDocumentor extension, and :doc:`custom-directive` before
    you continue this guide.

Continuing the ``.. hello::`` example from :doc:`custom-directive`, our node simply needs to carry the name that was
passed to the directive so that a template can use it later:

.. include:: ./examples/Directive/HelloNode.php
    :code: php

Nodes extend :php:class:`phpDocumentor\\Guides\\Nodes\\AbstractNode`, which already takes care of common concerns
such as options (``:option: value`` on the directive) and CSS classes. The only thing our ``HelloNode`` adds is a
convenient ``getName()`` accessor around the value that is stored by the base class.

.. hint::

    If your directive needs access to information gathered elsewhere in the project, for example data from the
    parsed API documentation, look at :php:class:`phpDocumentor\\Guides\\Nodes\\PHP\\DescriptorNode`. It is a more
    advanced base node that gives you access to a ``Descriptor``, and is outside the scope of this guide.

With a node in place, the last step is telling phpDocumentor how to render it, which is what :doc:`node-templates`
covers.
