Extensions
==========

Introduction
------------

Extensions serve provide third parties with the possibility to add new
functionality to phpDocumentor.

.. note::

   Extensions are a successor to plugins. This module aims to provide an easier
   and more flexible way of interacting with phpDocumentors core and extending
   it.

What can you do with an extension?
----------------------------------

* Add new tags (both inline and normal)
* Add new templates
* Add more validations
* Output the data in another format using :doc:`../for-template-builders/writers`
* React to events in the code and alter the generated content based on that
* Alter the table of contents
* Export the reflected information (structure) to a different format
* Create a bundle of your favourite extensions

Design
------

With extensions you can provide additional or alternate classes for aspects
such as:

* Tags
* Exporters
* Writers

Basics
~~~~~~

Extensions operate by implementing the ``mediator`` and ``observer`` design
patterns. This concept is often seen as Event Dispatchers.

Custom tags
~~~~~~~~~~~

With regards to tags we want to make it even simpler: every tag should be parsed
by a default ``Tag\Definition`` to separate the tag name from the description
and throw an event so that the description may be separated into smaller pieces
by registering an event with the dispatcher.

.. note::

   To help support annotations we should be able to easily expand the tag name
   according to the namespace rules.