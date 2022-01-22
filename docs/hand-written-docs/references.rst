##########
References
##########

.. include:: ../includes/guides-disclaimer.rst

One of the key features of a well written documentation set are links to navigate between sections in your documentation.
This page describes the by phpDocumentor supported references. Those references allow you to link to different parts of
your documentation set or even between documentation sets when you have multiple sets.

Doc
===

Most used links you create in `Guides`_ are refering to other pages or sections of your documentation. ReStructuredText
supports multiple formats of links. But most common used is the notation below.

.. code-block:: rst
    :doc:`Title of your page`

The code above will search full documentation for the given title ``Title of your page``. and automatically create a link
to this page. The downside of this, your titles have to be unique to be able to link to a specific document. In large
projects it will be nearly impossible to do this.

.. note::
    :ref:`Configuration`

PHP
===

References classes:

:php:class:`phpDocumentor\Descriptor\ClassDescriptor`

Reference methods

:php:method:`phpDocumentor\Descriptor\ClassDescriptor::getParent()`

Reference properties

:php:property:`phpDocumentor\Descriptor\ClassDescriptor::$methods`

Reference namespaces

:php:namespace:`phpDocumentor\Descriptor`
