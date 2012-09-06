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
   location.
3. Alter the template.css in the css sub-folder to apply your own styling and/or
   alter the index.xsl to accommodate for your own layout.

   **Important:** Unless you are absolutely sure what you are doing; please keep
   the tabular structure intact because a 100% height scenario is desirable and
   due to the need for iframes it is necessary to use a tabular structure
   instead of the box model.

   ..

       If you know a solution to change the tabular structure into box model;
       please contact us via the methods mentioned in the README file.
