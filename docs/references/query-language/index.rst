##############
Query language
##############

The query language phpDocumentor is using is inspired by jsonpath_. This page will explain the syntax and give you
examples on how to use it in your documentation. The query language can be used in different directives to create
living lists of elements and structures, which are updated when you regenerate your documentation. By using the query
you will safe yourself a lot of time keeping your documentation up to date.

Queries are always executed on the :abbreviation:`AST (Abstract Syntax Tree)` of the documentation. This means that you can query
any element in your documentation. However this guide is mostly focused on querying php elements from the api documentation.
Which is the most common use case.

.. contents::
   :local:
   :depth: 2

Basic syntax
============

+--------+-----------------------------------------------------------------------------------------------------------------+
| Symbol | Description                                                                                                     |
+========+=================================================================================================================+
| ``$``  | The root element to query. This is the starting point of the query.                                             |
+--------+-----------------------------------------------------------------------------------------------------------------+
| ``@``  | The current element to query. This is the starting point of a subquery.                                         |
+--------+-----------------------------------------------------------------------------------------------------------------+
| ``.``  | The dot-notation is used to select a child element.                                                             |
+--------+-----------------------------------------------------------------------------------------------------------------+
| ``[]`` | The square brackets are used to select a child element based on a condition.                                    |
+--------+-----------------------------------------------------------------------------------------------------------------+
| ``*``  | The asterisk is used to select all child elements.                                                              |
+--------+-----------------------------------------------------------------------------------------------------------------+
| ``?()``| Applies a filter (script) expression.                                                                           |
+--------+-----------------------------------------------------------------------------------------------------------------+

Basic examples
==============

Most queries you execute will start with a selector to select the :php:class:`phpDocumentor\Descriptor\ApiSetDescriptor`.
For your convenience this selector is already part of many directives that focus on the api documentation. If you ever
need to select the api documentation you can use the following selector::

    $.documentationSets.*[?(type(@) == 'ApiSetDescriptor')]

This selector will select all :php:class:`phpDocumentor\Descriptor\ApiSetDescriptor` elements from the documentation.
From here you can select any element in your code base using the query language. Let's say you want to select all
classes from your code base. You can use the following selector::

    $.documentationSets.*[?(type(@) == 'ApiSetDescriptor')].indexes.classes.*

As you can see the ``.`` is used to select a child element by name. This works for all methods on our :abbreviation:`AST (Abstract Syntax Tree)`
and also on indexes of collections. Alternatively you can use the ``[]`` to select a child element. So a equal
selector would be::

    $.documentationSets.*[?(type(@) == 'ApiSetDescriptor')].indexes.['classes']

Filtering elements
==================

It is more likely that you want to filter elements from your documentation. For example you want to select all classes
that implement a certain interface or extend a certain class. This is where the ``[]`` selector comes in handy. Filters
can be complex expressions and can be combined with the ``&&`` and ``||`` operators. And some basic functions are available
to make it easier to select values based on their type.

A filter is always executed on an collection of elements and can only be part of a ``[]`` selector::

    [?(@.inheritedElement == "\phpDocumentor\Transformer\Writer\WriterAbstract")]

This filter will select all classes that extend the :php:class:`phpDocumentor\Transformer\Writer\WriterAbstract` class.
The ``@`` symbol is used to select the current element in the collection.

Operators
---------

The following operators are available to use in your filters:

+-----------------+---------------------------------------------------------------+
| Symbol          | Description                                                   |
+=================+===============================================================+
| ``==``          | Checks if the left and right side are equal,                  |
|                 | note that strict comparision is used. So you can not compare  |
|                 | a string with an integer.                                     |
+-----------------+---------------------------------------------------------------+
| ``!=``          | Checks if the left and right side are not equal,              |
|                 | note that strict comparision is used. So you can not compare  |
|                 | a string with an integer.                                     |
+-----------------+---------------------------------------------------------------+
| ``starts_with`` | Checks if the left side starts with the right side.           |
+-----------------+---------------------------------------------------------------+

.. _jsonpath: http://goessner.net/articles/JsonPath/
