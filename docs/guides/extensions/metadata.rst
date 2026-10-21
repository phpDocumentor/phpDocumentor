###############
Extend api docs
###############

.. include:: include.rst.txt

phpDocumentor can attach extra information, called *metadata*, to the elements it reflects. An extension can produce
that metadata and provide a template that shows it in the generated documentation.

In this tutorial we build an extension that finds WordPress ``do_action()`` calls and lists them on the page of the
method or function that fires them:

.. code-block:: php

    class Post
    {
        public function save(): void
        {
            /** Fires after a post was saved. */
            do_action('save_post');
        }
    }

The page of ``Post::save()`` will get a "Hooks" section that contains ``'save_post'`` and its summary.

.. note::

    Read first :ref:`how to setup<setup-extension>` a phpDocumentor extension before you continue this guide.

Metadata is rendered on the pages of classes, interfaces, traits, enums, functions, methods and files. Namespaces
are not supported.

The extension consists of two parts: producing the metadata while the code is reflected, and rendering it while the
documentation is written. Start with the manifest and the extension class:

.. include:: ./examples/Hooks/manifest.xml
    :code: xml

.. include:: ./examples/Hooks/Extension.php
    :code: php

The alias returned by ``getAlias()`` is also the name of the Twig namespace of your templates, so use only letters,
numbers and ``-``. We come back to ``getTemplatePath()`` and ``getMetadataKeys()`` in step 2.

Step 1: Produce the metadata
----------------------------

Metadata is an object that implements ``phpDocumentor\\Reflection\\Metadata\\Metadata``. Its ``key()``
identifies the kind of metadata. The format of the rest of the object is up to you. Ours holds a list of actions:

.. include:: ./examples/Hooks/Hooks.php
    :code: php

.. include:: ./examples/Hooks/Action.php
    :code: php

Next we need something that finds ``do_action()`` calls and adds the metadata to the element that contains them. This
is a reflection *strategy*. It receives the parsed PHP nodes, and decides with ``matches()`` whether it handles one:

.. include:: ./examples/Hooks/ActionFactory.php
    :code: php

- ``matches()`` accepts a function call to ``do_action()`` (or a variant) while the element being reflected, on top
  of the context stack, can hold metadata.
- ``doCreate()`` adds a ``Hooks`` object to that element, if it does not have one yet, and records the action in it.
  The docblock above the call becomes the description of the action.

Register the strategy in the container with the ``phpdoc.reflection.strategy`` tag:

.. include:: ./examples/Hooks/services.php
    :code: php

Step 2: Render the metadata
---------------------------

phpDocumentor renders metadata with a template that belongs to the extension. For this the extension class needs
two things, which you already saw above:

- ``getTemplatePath()`` returns the directory that contains your templates. phpDocumentor makes it available as the
  Twig namespace ``@wordpress-hooks``, named after the alias of the extension.
- ``getMetadataKeys()`` lists the keys of the metadata your extension renders. Metadata with a key that no extension
  lists is not rendered.

Each metadata object is rendered with the template ``@<alias>/metadata/<key>.html.twig``. Our key is ``wp_hooks``, so
the template is ``Resources/templates/metadata/wp_hooks.html.twig``:

.. include:: ./examples/Hooks/wp_hooks.html.twig
    :code: twig

The template receives two variables:

``metadata``
    The metadata object of your extension, in our case the ``Hooks`` object.

``node``
    The descriptor that the metadata is attached to, such as the method.

The output of all templates is placed in a ``<section class="phpdocumentor-metadata">`` on the page. When nothing
is rendered, the section is left out.

Step 3 (optional): Link to it from "On this page"
-------------------------------------------------

The pages of classes, interfaces, traits, enums, and files list their sections in the "On this page" box. Metadata
on the element of the page, such as a class, can add an entry there. Metadata on a method, like our hooks, cannot:
methods have no entries of their own in the box. To add an entry, provide the template
``@<alias>/on-this-page/<key>.html.twig``. It receives the same ``metadata`` and ``node`` variables, and renders a list
item:

.. code-block:: twig

    <li><a href="{{ link(node) }}#wp-hooks">Hooks</a></li>

The link needs an anchor in the template of step 2, such as ``id="wp-hooks"``. The template is optional: without it,
the metadata has no entry in "On this page", and no warning is logged. Each extension adds the entries for its own
keys, so several extensions can add entries to the same page.

Try it
------

Install the extension, run phpDocumentor on code that calls ``do_action()`` and open the page of the method. You
will see the "Hooks" section below the other information of the method.

Troubleshooting
---------------

phpDocumentor logs a warning for each metadata object that is not rendered, so run it with ``-v`` to see them:

- *no extension declares this key*: add the key to ``getMetadataKeys()``.
- *extension has no template*: create ``metadata/<key>.html.twig`` in the template path of the extension.
- *template path is not a directory*: check that ``getTemplatePath()`` points to an existing directory.
