Quickstart
==========

.. hint::

   A lot has changed since this text was written; it is still relevant but could use improvement.

I will give a step-by-step description how to create your own branding:

1. Create a new folder in your intended location (for example your project) with
   the name of the intended template.

   ..hint::

       Please try to prevent having spaces in the path; some writers, such as the XSL Writer, cannot handle that.

2. Generate a skeleton template by invoking the following command::

       $ phpdoc template:generate -t <target path> --name <template name>

   .. hint::

       Optionally you can also provide ``--author`` to provide an author name and
       ``--version`` to provide a version number to your template.

3. Alter the template.css in the css sub-folder to apply your own styling and/or
   alter the index file to accommodate for your own layout.

4. To use your new template, it needs to be installed in the source directory
   of phpDocumentor in the data/templates directory. If you installed phpDocumentor through PEAR on Linux this
   will typically be /usr/share/php/phpDocumentor/data/templates

   During development of the template, you can also specify the template name
   in your phpdoc.xml as path to your development folder. Then the phpdoc
   command will always copy your template to the templates directory first.
   If you installed phpDocumentor through PEAR this means you need to change permissions
   on the templates folder or run phpDocumentor as root.
