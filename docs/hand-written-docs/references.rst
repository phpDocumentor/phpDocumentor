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

The code above will search full documentation for the given title ``Title of your page``. and automatically create a
link to this page. The downside of this, your titles have to be unique to be able to link to a specific document. In
large projects it will be nearly impossible to do this.

PHP
===

It is also possible to link to elements from the API documentation by using the ``:php:[TYPE]:`` notation.

For example:

* classes: ``:php:class:`phpDocumentor\Descriptor\ClassDescriptor```
* methods: ``:php:method:`phpDocumentor\Descriptor\ClassDescriptor::getParent()```
* properties: ``:php:property:`phpDocumentor\Descriptor\ClassDescriptor::$methods```
* or even namespaces: ``:php:namespace:`phpDocumentor\Descriptor```

By default, the text will be the complete FQSEN of that element. But similar to other references it is allowed
to provide an alternate text to be displayed:

.. code:: rst

   :php:class:`AlternateText<phpDocumentor\Descriptor\ClassDescriptor>`
