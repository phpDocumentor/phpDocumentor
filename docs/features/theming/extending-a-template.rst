####################
Extending a template
####################

Building a template can take a lot of time, we did our best to provide a lot of hooking points to make it easy for
you to change the template to your needs. However if you need to change the template drastically, you can extend it.
Extending a template means that you create a new template include the original template transformations.
This way you can add extra pages to your documentation or create custom reports. This requires knowledge of the descriptor
structure of phpDocumentor. And we see this as an advanced feature.

First step is to create a new template. You can place this template anywhere you want as long phpDocumentor can read it.
A minimal template should have an ``template.xml`` file just like any other template. We recommend to put this file in
the ``.phpdoc/template`` directory in your project directory, so you keep it close to your project.

This example will extend the ``default`` template.

.. code-block:: xml

    <?xml version="1.0" encoding="utf-8"?>
    <template>
        <name>phpdoc</name>
        <author>Jaap van Otterdijk</author>
        <email>jaap@phpdoc.org</email>
        <version>1.0.0</version>
        <extends>default</extends>
        <transformations>
            <!-- Add your transformations here -->
        </transformations>
    </template>

Now you have a template, we can add it to the configuration file. Add the following to your ``phpdoc.dist.xml`` file.

.. code-block:: xml
    <phpdocumentor>
        <!-- your other configuration -->
        <template name=".phpdoc/template" />
    </phpdocumentor>

Now you can add your own transformations to the template. In this example we will add a custom index page
to the documentation.

.. code-block:: xml

        <transformations>
            <transformation writer="twig" source="custom.html.twig" artifact="index.html"/>
        </transformations>

The transformation will use the ``twig`` writer to transform the ``custom.html.twig`` file to ``index.html``. The
``custom.html.twig`` file should be placed in the ``.phpdoc/template`` directory. Or at the same level as the
``template.xml`` file.
