Quickstart
==========

I will give a step-by-step description how to create your own branding:

1. Create a new folder in your intended location (for example your project) with
   the name of the intended template. Please make sure *no* spaces are present in
   the path as the XSL Writer (or actually libxsl) cannot handle that.
2. invoke the following command::

       $ phpdoc template:generate -t <target path> --name <template name>

   ..

       Optionally you can also provide **--author** to provide an author name and
       **--version** to provide a version number to your template.

   The command above will generate a skeleton template for you in the intended
   location that extends the abstract base template.
3. Alter the template.css in the css sub-folder to apply your own styling and/or
   alter the index.xsl to accommodate for your own layout.

   **Important:** Unless you are absolutely sure what you are doing; please keep
   the tabular structure intact because a 100% height scenario is desirable and
   due to the need for iframes it is necessary to use a tabular structure
   instead of the box model.

   ..

       If you know a solution to change the tabular structure into box model;
       please contact us via the methods mentioned in the README file.

4. To use your new template, it needs to be installed in the source directory
   of phpDocumentor in the data/templates directory. If you installed phpDocumentor through PEAR on Linux this
   will typically be /usr/share/php/phpDocumentor/data/templates

   During development of the template, you can also specify the template name
   in your phpdoc.xml as path to your development folder. Then the phpdoc
   command will always copy your template to the templates directory first.
   If you installed phpDocumentor through PEAR this means you need to change permissions
   on the templates folder or run phpDocumentor as root.
