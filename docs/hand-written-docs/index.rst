##########################
Hand written documentation
##########################

.. include:: ../includes/guides-disclaimer.rst

PhpDocumentor is focusing on all documentation for your projects. API documentation will give insights on the code base
of your project. But it might be hard to an overview of your project's architecture by just looking at the class API's.
Or you need a written piece of text to explain a concept of your project, write a user manual. All this extra
information is what we call ``Hand written documentation``, or `Guides`_ in short.

Guides are documents that belong to your project and can be written in `ReStructuredText`_. phpDocumentor will render
these guides together with your project's class documentation. But is also able to create separate documentation sets
for your non-tech readers.

.. hint::

   Why RestructuredText? In our opinion it is superior to, for example, Markdown in every respect; with things like
   support for these hint blocks, customizability built-in into the language and a consistent syntax. But more-over,
   most of the project documentation efforts currently use RestructuredText.

   Does that mean we hate Markdown? Not at all! Markdown support is on our roadmap for guides -using the CommonMark
   dialect- but we are solidifying features using RestructuredText and branching out after that.

.. toctree::
   :maxdepth: 2

   references
