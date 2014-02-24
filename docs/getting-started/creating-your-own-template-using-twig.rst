Creating your own template using Twig
=====================================

Overview
--------

With this tutorial I want to provide an short tutorial on how to build templates in phpDocumentor using the Twig
templating engine.

phpDocumentor's templating system is powerful and flexible enough to produce any kind of output or even multiple types
of output combined into one template. During this tutorial we will guide you how to create a simple template that will
produce HTML files that you can serve from your website.

While building a template there are three steps that are important:

1. Creating a template definition file.
2. Defining all :term:`transformations` that your template must do.
3. Writing your twig templates and providing assets.

Each of these steps is covered in the following subchapters, so if you want to make your own template you can just read
on and follow along with the text.

.. important::

   In this tutorial I assume that you have worked with Twig before and know how it works. If you are unclear on how
   some things work, please consult the Twig documentation.

Create your template definition
-------------------------------

Simply put, a template is a combination of :term:`transformations` that will read the data that phpDocumentor has
aggregated and transform that into the output that you desire, such as an HTML page.

In order for phpDocumentor to know what to do there is always a template definition file present, called
``template.xml``, in the root of your template. This file contains some meta-data and a series of transformation
definitions that are executed sequentially.

Let's look at a very simple example:

.. code-block:: xml

   <?xml version="1.0" encoding="utf-8"?>

   <template>
        <author>Mike van Riel</author>
        <email>me@mikevanriel.com</email>
        <description>This is a description that will inform the user what to expect from your template</description>
        <version>1.0.0</version>
        <transformations>
            <transformation writer="FileIo" query="copy" source="templates/responsive/css" artifact="css"/>
            <transformation writer="twig" source="templates/responsive-twig/index.html.twig" artifact="index.html"/>
        </transformations>
    </template>

The meta-data of this template is rather straight-forward but we will shortly describe them here for context:

1. author, defines the name of the author of this template. This field may be repeated if there are several authors.
2. email, the e-mail address of the maintainer for this template (only one is expected).
3. description, a short piece of text that will inform the person who wants to use this template what to expect.
4. version, a version number according to Semantic Versioning that will describe at what the current version of the
   template is.

The transformations collection is far more interesting and worth going into more detail. The next chapter will go into
much more detail on how transformations work.

Defining transformations
------------------------

In the previous chapter I had shown an example of what a simple template could look like. In that example you could see
a ``transformations`` element with two transformations. Let's continue from there and see what a transformation is and
how to create one.

The definition of a transformation is an action that transforms one type of data to another. Practically this means
that one transformation can copy a file, generate an HTML file from data aggregated by phpDocumentor, generate a graph
from data in phpDocumentor and other equal tasks. As long as data is input and data is output then you can do it with
a transformation.

Let's look at the transformations definition from the previous chapter again:

.. code-block:: xml

    <?xml version="1.0" encoding="utf-8"?>

    <template>
        ...
        <transformations>
            <transformation writer="FileIo" query="copy" source="templates/responsive/css" artifact="css"/>
            <transformation writer="twig" source="templates/responsive-twig/index.html.twig" artifact="index.html"/>
        </transformations>
    </template>

In this definition we can spot two transformations with each several attributes. It is these attributes that influence
the behaviour of said transformations.

.. sidebar:: Base folders

   **Source folder**

   Although the base location of the source folder may differ per writer it is a common pattern that it matches the
   templates folder of phpDocumentor. This has a historical reason.

   phpDocumentor started out with supporting only XSL as a templating engine but XSL can only extend files that it can
   physically find in the template. You cannot tell it to scan a separate folder. So for XSL to be able to extend
   existing templates all templates are being put inside the templates folder of phpDocumentor, even your own custom
   template when you invoke it.

   **Artifact folder**

   A with the source folder, the artifact folder may differ per writer. But a common pattern is that this location is
   relative to the target location that you provided phpDocumentor using the ``-t`` or ``--target`` option.

If we look closely at the first transformation than we can distinguish four properties:

1. Writer, which is a reference to a specialized class that will actually perform the transformation.
2. Query, an attribute which allows you to guide the transformation or limit it to a subset of the provided data.
3. Source, which is a reference to the input. This can be a template file, origin location (in case of a copying
   action) or any other data location.
4. Artifact, the destination folder, file or object where the output for this transformation is written to.

So. What does this transformation do?

    It uses the "FileIO" writer, which is used for disk operations, to "copy" the contents of the
    "templates/responsive/css" source folder to the "css" destination folder.

How the query, source and artifact attribute exactly function differs per Writer but this is the general use for them.

As another excercise, let's look at the second transformation in our example.

    This transformation uses the "Twig" writer, which is used to generate physical text-based files from Twig template
    files, to create the "index.html" artifact in your target folder using the "index.html.twig" template in the
    "templates/responsive-twig" folder of phpDocumentor's data folder.

What you might notice is that we do not have a query attribute in this transformation. Only the writer attribute is
required and all others can be omitted when necessary. This does not mean that the writer cannot use this attribute, it
is just not there because it is not used.

For example: the Twig writer can use the query attribute to only sent a bit of the aggregated data to the twig template.

Writing template files
----------------------

If I have done my job well then by now you know how to create a template, which meta-data to add to the template and
how to define transformation steps.

This is just one side of the coin. Because now we have to create the actual Twig template files which we can use to
generate HTML documents. Please note that this is a tutorial, we won't cover every bit in detail. If you want to know
in-depth what options are supported, please read the guide and browse through existing templates.

Generating a series of output files
-----------------------------------

Read more
---------

* :doc:`../guides/templates`
* :doc:`../references/writers/index`
