##############
Twig extension
##############

.. include:: include.rst.txt

phpDocumentor uses `twig <https://twig.symfony.com/>`_ for most rendering of the output. Twig gives us the flexibility
we need and allows you to customize just what you need in a template. For more advanced use-cases twig supports
`extensions <https://twig.symfony.com/doc/3.x/advanced.html>`_, these give you more options to write custom behavior
that impacts the output of phpDocumentor.

.. note::
    read first :ref:`how to setup<setup-extension>` an phpDocumentor extension before you continue this guide.

Now you have setup your phpDocumentor extension it is time to create a twig extension. In our example we will add
a custom `filter <https://twig.symfony.com/doc/3.x/advanced.html#filters>` which can be used in a template to transform text.
We can do this by creating a class like this:

.. include:: ./examples/Twig/MyExtension.php
    :code: php

Register the twig extension
---------------------------

A twig extension needs to be registered in phpDocumentor before you can use it in your templates. To do so we need
to add a new definition in the service container. With the tag :code:`twig.extension`

.. include:: ./examples/Twig/Extension.php
    :code: php

Using the filter
----------------

Once your phpDocumentor extension is ready you can start using it in your template. This works for complete custom templates
as well for :ref:`small overwrites <template-replace-component>`.
