Building your own branding using templates
==========================================

Most of the time it is only necessary to replace the graphical
layout with that of your own project. This is an easy process and
requires little explanation on how templating actually works.
Please consult the chapter *Quick version* when the above is
what you desire.

Do you want to control every detail of how your template looks then
it is recommended to read the chapter *Elaborate version*; this
will provide more theory on how branding works in DocBlox.

Quick version
-------------

I will try to give a step-by-step description how to create your own branding:

1. Create a new folder in your intended location (for example your project) with
   the name of the intended template. Please make sure *no* spaces are present in
   the path as the XSL Writer (or actually libxsl) cannot handle that.
2. invoke the following command::

       $ docblox template:generate -t <target path> -n <template name>

   ..

     Optionally you can also provide **--author** to provide an author name and
     **--version** to provide a version number to your template.

   The command above will generate a skeleton template for you in the intended
   location.
3. Alter the template.css in the css sub-folder to apply your own styling and/or
   alter the index.xsl to accomodate for your own layout.

   **Important:** please place your code in the tabular structure, thus your
   head in the designated cell, your content in the designated cell and your
   footer in the designated cell.
   Because a 100% height scenario is desirable and due to the need for iframes
   it is necessary to use a tabular structure instead of the box model.

   ..

     If you know a solution to change the tabular structure into box model;
     please contact us via the methods mentioned in the README file.
