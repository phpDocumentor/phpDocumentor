Writing Directives
==================

What is a Directive
-------------------

A directive is a Component that can be used in RestructuredText. Directives offer the possibility to describe a special
Block element; such as an image, admonition, UML graph, etc. Directives can also have options, with which you can
influence their rendering.

For more information, check the specification at https://docutils.sourceforge.io/docs/ref/rst/directives.html.

In the context of this implementation, a Directive is responsible for converting the given type, options and block's
contents into a ``Node``. This node is subsequently responsible for rendering the given properties using an associated
``NodeRenderer``.

The Directive itself should not have to deal with the rendering process; it returns either a node, or performs an
action.

.. note::
   The term _should_ already gives it away a little. The initial parser did not properly distinguish between parsing and
   rendering. This means that some Directives, especially SubDirectives, _do_ produce rendered output. We hope to
   refactor this away.

Examples
--------

.. code-block::
    .. DANGER::
       Beware killer rabbits!

.. code-block::
    .. image:: picture.jpeg
       :height: 100px
       :width: 200 px
       :scale: 50 %
       :alt: alternate text
       :align: right

Writing a Directive
-------------------

The first step, is to create a class in ``src/Guides/RestructuredText/HTML/Directives`` that extends either
``phpDocumentor\Guides\RestructuredText\Directives\Directive`` or
``phpDocumentor\Guides\RestructuredText\Directives\SubDirective``.

The difference between these two is whether you want the contents of the Directive to be parsed, similar to a paragraph
or a whole Document, in which case you use a SubDirective; or a simpler Directive such as image or figure, in which
case you use the Directive class.

A Simple Directive
~~~~~~~~~~~~~~~~~~

Having a class that extends ``phpDocumentor\Guides\RestructuredText\Directives\Directive``, you need to implement the
following two methods:

1. ``getName()``, that should return the directive name or keyword; such as ``image``.
2. ``processNode()``, that should return an instance of a Node using the given Parser, based on the variable name
   (keyword) data in the Directive block and options set in the Directive block (if any).

One of the clearest examples is the ``Image`` directive that uses the given data to construct an ImageNode and
return that.

Actions
~~~~~~~

As was hinted before, a Directive need not return a ``Node``. These can also influence the current document, which is
called an 'Action'.

Examples of these are:

- Bibliographic fields
- HTML Meta fields
- The ``URL`` Directive (seen in the ``phpDocumentor\Guides\RestructuredText\HTML\Directives\Url`` class)

This last directive if a straight forward example of how to create a simple Action.

Complex Directives
~~~~~~~~~~~~~~~~~~

At time of writing, not much is known on what this exactly is or does. I infer it has to do with re-parsing the block
(like you'd do with a paragraph) since Directives such as admonitions can be nested. But so far it is not clear yet.

When I find out, I will write it down.

.. include:: includes/sub-directive.rst
   :literal:
