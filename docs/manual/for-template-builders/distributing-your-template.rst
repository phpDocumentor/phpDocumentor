Distributing your template
==========================

.. hint::

    To build a template specific to your project, you don't need to put it into
    a separate repository but can distribute it with your project. Simply specify
    the path to that template as name of the template to use.

To build a standalone phpDocumentor2 template, you need to make it a
`Composer <http://getcomposer.org>`_ package, like every component of phpDocumentor.

The project name in the composer file must start with ``template-``, the template name
used in the install will be what follows after. For example the template "new-black"
has the name ``template-new-black``. A template also needs to specify the attribute
``type: phpdocumentor-template``. Currently, all templates must be in the namespace
``phpdocumentor``.

Templates must depend on the phpdocumentor/unified-asset-installer which is
used to install them in the right location. If they extend a base template,
this should be specified as well.

As an example, see the composer.json of the new-black template:

.. code-block:: json

    {
        "name":        "phpdocumentor/template-new-black",
        "type":        "phpdocumentor-template",
        "description": "Web 2.0 template with dark sidebar for phpDocumentor",
        "keywords":    ["documentation", "template", "phpdoc"],
        "homepage":    "http://www.phpdoc.org",
        "license":     "MIT",
        "require": {
            "phpdocumentor/unified-asset-installer": "1.*",
            "phpdocumentor/template-abstract":       "1.*"
        }
    }
